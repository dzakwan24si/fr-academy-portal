<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'required_without:nik', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'nik' => ['nullable', 'required_without:email', 'string', 'max:255', 'unique:users,nik,'.$user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'email.required_without' => 'Alamat Email wajib diisi jika NIK tidak diisi.',
            'nik.required_without' => 'NIK wajib diisi jika Alamat Email tidak diisi.',
            'email.unique' => 'Alamat Email sudah terdaftar.',
            'nik.unique' => 'NIK sudah terdaftar.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'nik' => $validated['nik'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}
