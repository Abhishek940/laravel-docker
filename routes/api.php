<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\leaveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::get('/products', [ProductController::class, 'getProducts']);

use Illuminate\Support\Facades\DB;

Route::get('/db-test', function () {
    return [
        'host' => config('database.connections.mysql.host'),
        'port' => config('database.connections.mysql.port'),
        'database' => config('database.connections.mysql.database'),
        'connection' => DB::connection()->getPdo() ? 'CONNECTED' : 'FAILED',
    ];
});

Route::get('/fetch', [ProductController::class, 'fetch']);


Route::get('/db-test', function () {
    return [
        'host' => config('database.connections.mysql.host'),
        'port' => config('database.connections.mysql.port'),
        'database' => config('database.connections.mysql.database'),
        'connection' => DB::connection()->getPdo() ? 'CONNECTED' : 'FAILED',
    ];
});

Route::middleware('auth:api')->group(function () {
// product routes    
//Route::get('/products', [ProductController::class, 'getProducts']);
Route::post('/add/product',[ProductController::class,'addProduct']);
Route::get('/products/{id}', [ProductController::class, 'getProductById']);
Route::post('/products/update/{id}', [ProductController::class, 'updateProduct']);
Route::delete('/products/{id}', [ProductController::class, 'deleteProduct']);
// leave routes

Route::post('/add/leave',[leaveController::class,'Addleave']);
Route::get('/leave/lists',[leaveController::class,'getleaves']);
Route::get('/leave/{id}',[leaveController::class,'getleaveById']);
Route::post('/update/leave/{id}',[leaveController::class,'updateleave']);
Route::delete('/delete/leave/{id}',[leaveController::class,'deleteleave']);


Route::post('/logout',[AuthController::class,'logout']);

  Route::get('/profile',function(Request $request){
        return $request->user();
    });
});
