<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    function index()
    {
        // dd(Auth::guard('admin'));
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (!Auth::guard('admin')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        return redirect()->route('admin.coba');
    }

    public function logout(Request $request)
    {
        // dd('lohe');
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function coba(){
        return view('auth.admin.coba');
    }
}
