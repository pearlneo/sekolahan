<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);    

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg, 422);
        }

        $credentials = $request->only('email', 'password');
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->failedResponse('Email atau password salah!', 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged in.',
            'token' => $token
        ]);
    }
}