<?php

namespace App\Http\Controllers\Api;

use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('products', $imageName, 'public');
        }

        $productId = DB::table('products')->insertGetId([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ProductCreated::dispatch([
            'id' => $productId,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product inserted successfully.',
            'data' => ['id' => $productId],
        ], 201);
    }

    public function getProducts()
    {
        $products = DB::table('products')->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ], 200);
    }

    public function getProductById($id)
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ], 200);
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $imageName = $product->image;

        if ($request->hasFile('image')) {
            $imageName = time().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('products', $imageName, 'public');
        }

        DB::table('products')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'image' => $imageName,
                'updated_at' => now(),
            ]);

        ProductUpdated::dispatch([
            'id' => (int) $id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
        ]);
    }

    public function deleteProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        DB::table('products')
            ->where('id', $id)
            ->delete();

        ProductDeleted::dispatch([
            'id' => (int) $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'image' => $product->image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update($request->all());

        ProductUpdated::dispatch($product->fresh()->toArray());

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $payload = $product->toArray();
        $product->delete();

        ProductDeleted::dispatch($payload);

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    public function fetch(Request $req)
    {
        try {
            $taskId = $req->input('id', 0);
            $query = DB::table('tasklist')->where('deletedFlag', 0);
            if ($taskId > 0) {
                $query->where('id', $taskId);
            }
            $result = $query->orderBy('id', 'desc')->get();

            return response()->json([
                'data' => $result,
                'status' => '200',
                'total' => $result->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'message' => 'Something went wrong while fetching tasks',
                'status' => 500,
            ], 500);
        }
    }
}
