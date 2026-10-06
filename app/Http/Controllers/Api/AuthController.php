<?php
namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
   
public function register(Request $request)
{
    $request->validate([
        'name'=>'required',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:6'
    ]);

    $user = User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password)
    ]);

    return response()->json([
        'message'=>'Registered Successfully'
    ]);
}

public function login(Request $request){
    if(!Auth::attempt([
        'email' => $request->email,
        'password' => $request->password,
    ]))
    {
        return response()->json([
            'message' => 'Invalid Credentials'
        ],401);
    }

    $user = Auth::user();
    $token =$user->createToken('Api Token')->accessToken;
    return response()->json([
        'token' => $token,
        'user' => $user
    ]);
}

   public function logout(request $request) {
    $request->user()->token()->revoke();
    return response()->json([
        'message' => 'Logout Successfully'
    ]);
   }
}
