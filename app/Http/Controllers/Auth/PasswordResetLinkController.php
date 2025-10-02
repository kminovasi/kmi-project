<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required','string','max:191'], // email ATAU username
        ]);

        $identifier = trim($data['identifier']);

        // Deteksi email vs username
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;

        // Cari user berdasarkan email/username
        $user = User::query()
            ->when($isEmail, fn($q) => $q->where('email', $identifier))
            ->when(!$isEmail, fn($q) => $q->where('username', $identifier))
            ->first();

        // Tidak ditemukan → warning eksplisit
        if (!$user) {
            return back()
                ->withInput()
                ->with('error', $isEmail
                    ? 'Email tidak terdaftar.'
                    : 'Username tidak terdaftar.');
        }

        // User ada tapi tidak punya email (kasus username login tanpa email)
        if (empty($user->email)) {
            return back()
                ->withInput()
                ->with('error', 'Akun ini belum memiliki email terdaftar. Hubungi admin untuk menambahkan email.');
        }

        // Kirim link reset ke email user
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Tautan reset telah dikirim ke ' . $user->email . '.');
        }

        // Tangani kemungkinan throttle/invalid, dll.
        // (Laravel 9 biasanya mengembalikan pesan terjemahan bawaan)
        return back()->with('error', __($status));
    }
}
