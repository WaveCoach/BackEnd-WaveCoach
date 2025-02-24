<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginCheck(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = trim(strtolower($request->email));

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && $user->role_id == 1) {
            if (Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->remember)) {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah atau tidak memiliki akses']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login-user');
    }
}
