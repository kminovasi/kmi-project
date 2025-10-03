<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\HtmlString;


class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required','string','max:191'], 
        ]);

        $identifier = trim($data['identifier']);
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;

        $user = User::query()
            ->when($isEmail, fn($q) => $q->where('email', $identifier))
            ->when(!$isEmail, fn($q) => $q->where('username', $identifier))
            ->first();

        if (!$user) {
            return back()->withInput()->with('error', new HtmlString(
        'Email tidak terdaftar. Silakan hubungi <a href="mailto:kminovasi@sig.id">kminovasi@sig.id</a> untuk bantuan pendaftaran/aktivasi.'
        ));
        }

        if (empty($user->email)) {
            return back()->withInput()->with('error', new HtmlString(
        'Email tidak terdaftar. Silakan hubungi <a href="mailto:kminovasi@sig.id">kminovasi@sig.id</a> untuk bantuan pendaftaran/aktivasi.'
        ));
        }

        $status = Password::sendResetLink(['email' => $user->email]);
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Tautan reset telah dikirim ke ' . $user->email . '. Silakan cek folder inbox maupun spam');
        }
        return back()->with('error', __($status));
    }
}
