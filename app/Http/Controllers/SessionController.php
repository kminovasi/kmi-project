<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
    
    public function register(Request $request)
    {
        $email = $request->input('email');
        $existing = User::where('email', $email)->first();

        if ($existing) {
            // EMAIL SUDAH TERDAFTAR -> REPLACE PASSWORD
            $validated = $request->validate([
                'email'     => ['required','email:rfc,dns','max:150'],
                'password'  => ['required','string','min:8','confirmed'],
            ]);

            $existing->forceFill([
                'password' => Hash::make($validated['password']),
            ])->save();

            Auth::guard('web')->login($existing);
            Session::flash('success', __('Password berhasil diperbarui. Selamat datang kembali!'));
            return redirect()->intended('homepage');

        } else {
            // EMAIL BELUM TERDAFTAR -> BUAT USER BARU 
            $validated = $request->validate([
                'name'      => ['required','string','max:150'],
                'email'     => ['required','email:rfc,dns','max:150', Rule::unique('users','email')],

                'password'  => ['required','string','min:8','confirmed'],
            ]);

            $username = $request->input('username') ?: $validated['email'];

            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'username'  => $username,
                'password'  => Hash::make($validated['password']),
                'role'      => 'Innovator', 
            ]);

            Auth::guard('web')->login($user);
            Session::flash('success', __('Registrasi berhasil. Selamat datang!'));
            return redirect()->intended('homepage');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}