@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('login')
    @if (session()->has('loginErr'))
        <div class="tw-p-4 tw-mb-4 tw-text-sm tw-text-red-800 tw-rounded-lg tw-bg-red-50 tw-dark:bg-gray-800 tw-dark:text-red-400"
            role="alert">
            <span class="tw-font-medium">Login Gagal!</span> Ulangi kembali.
        </div>
    @endif

    <div class="container">
        <div class="top">
            <img id="logo-sig" src="{{ asset('assets/sig-logo.png') }}" alt="">
        </div>
        <div class="company-logo">
            <img id="logo-siggia" src="{{ asset('assets/sigialogo.png') }}" alt="">
        </div>
        <div class="form ">
            <div class="login tw-bg-gradient-to-r tw-from-red-700 tw-to-red-200 tw-drop-shadow-lg">
                <p id="login-text">Login to Your Account</p>
                <form id="login-form" method="post" action="login">
                    @csrf
                    <label for="username"></label>
                    @error('username')
                    <div class="errormessage">
                        {{ $message  }}
                    </div>
                    @enderror
                    <input class="tw-mb-6 @error('username') tw-invalid:bg-slate-50 tw-invalid:text-pink-600
                    @enderror " type="text" placeholder="username" name="username" id="username" required value="{{ old('username') }}">
                    <label for="username"></label>
                    <input class="tw-mb-6" type="password" placeholder="Password" name="password" id="password" required>
                    <button type="submit"
                        class="tw-mb-6 tw-bg-gradient-to-r tw-from-yellow-300 tw-from-10% tw-via-red-500 tw-via-50% tw-to-emerald-500 tw-to-90%"
                        id="login-submit">Submit</button>
                </form>
            </div>
        </div>
        <div class="foot">
            <img id="logo-tagline" src="{{ asset('assets/sig-tagline.png') }}" alt="">
        </div>
        <p id="copyright">Copyright 2023 All rights reserved PT Semen Indonesia</p>
    </div>
@endsection
