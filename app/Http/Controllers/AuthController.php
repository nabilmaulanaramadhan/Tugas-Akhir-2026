<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // dummy login, langsung redirect ke dashboard
        return redirect()->route('dashboard');
    }

    public function logout()
    {
        return redirect()->route('login');
    }
}
