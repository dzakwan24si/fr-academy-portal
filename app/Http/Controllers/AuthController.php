<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Tambahkan class Http untuk request API

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);

        // 1. Validasi input
        $rules = [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        $messages = [];

        if ($turnstileEnabled) {
            $rules['cf-turnstile-response'] = ['required', 'string'];
            $messages['cf-turnstile-response.required'] = 'Harap selesaikan verifikasi keamanan Cloudflare.';
        }

        $request->validate($rules, $messages);

        // 2. Kirim token ke API Cloudflare untuk divalidasi keasliannya jika Turnstile diaktifkan
        if ($turnstileEnabled) {
            $http = Http::asForm();
            if (app()->isLocal()) {
                $http = $http->withoutVerifying();
            }
            $turnstileResponse = $http->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret', '0x4AAAAAAEzPGKSsKiRsoVbsX1tzF-N7x8k'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);
            if (! $turnstileResponse->json('success')) {
                return back()->withErrors([
                    'identifier' => 'Sistem mendeteksi aktivitas mencurigakan. Silakan refresh halaman dan coba lagi.',
                ])->onlyInput('identifier');
            }
        }

        // 3. Lanjutkan ke proses login
        $identifier = $request->input('identifier');

        $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        if (Auth::attempt([$fieldType => $identifier, 'password' => $request->input('password')], $request->boolean('remember'))) {
            $user = Auth::user();

            if (! ($user->is_active ?? true)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'identifier' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator sistem.',
                ])->onlyInput('identifier');
            }

            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            // Arahkan ke landing page sesuai hak akses dan peran (role) pengguna
            return redirect($user->getDefaultLandingUrl());
        }

        return back()->withErrors([
            'identifier' => 'Email atau NIK atau password salah.',
        ])->onlyInput('identifier');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
