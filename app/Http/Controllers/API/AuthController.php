<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->SuccessResponse(['token' => $token, 'user' => $user], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->SuccessResponse([], 'Logged out successfully');
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('coach');
        return $this->SuccessResponse(['user' => $user], 'User profile retrieved successfully');
    }
}
