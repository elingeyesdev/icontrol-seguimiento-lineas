<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login (Request $request)
    {
           if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
           }
            $user=Auth::user();
            $token = $user->createToken('flutter')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
            
    }
}