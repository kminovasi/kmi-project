<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class SessionController extends Controller
{
    function index()
    {
        return view('layouts.login');
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => ['required'],
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        $data = array(
            'username' => $request->username,
            'password' => $request->password,
            'token' => env('SSO_TOKEN'),
        );

        $remember = $request->remember_me;

        // ketika fail dari sso
        $user = User::where('username', $credentials['username'])->first();
        // dd($user);
        if (is_null($user)) {
            Session::flash('error', __('User Tidak Ditemukan'));
            return back();
        }

        if (!Auth::guard('web')->attempt($credentials, $remember)) {
            Session::flash('error', __('Password Salah'));
            return back();
        }

        if ($user->role === 'Admin' || $user->role === 'Superadmin') {
            return redirect()->intended('dashboard');
        }

        return redirect()->route('homepage');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}