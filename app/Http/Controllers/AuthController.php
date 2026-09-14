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
        // 1. Tambahkan validasi untuk input token Turnstile
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
            'cf-turnstile-response' => ['required', 'string'], 
        ], [
            'cf-turnstile-response.required' => 'Harap selesaikan verifikasi keamanan Cloudflare.'
        ]);

        // 2. Kirim token ke API Cloudflare untuk divalidasi keasliannya
        $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => '0x4AAAAAAEzPGKSsKiRsoVbsX1tzF-N7x8k', 
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        // 3. Jika validasi dari Cloudflare gagal (bot/token kedaluwarsa)
        if (! $turnstileResponse->json('success')) {
            return back()->withErrors([
                'identifier' => 'Sistem mendeteksi aktivitas mencurigakan. Silakan refresh halaman dan coba lagi.',
            ])->onlyInput('identifier');
        }

        // 4. Jika Turnstile sukses, lanjutkan ke proses login asli milikmu
        $identifier = $request->input('identifier');
        
        $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        if (Auth::attempt([$fieldType => $identifier, 'password' => $request->input('password')], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
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