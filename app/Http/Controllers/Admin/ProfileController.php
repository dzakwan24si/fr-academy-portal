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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'nik' => ['nullable', 'string', 'max:255', 'unique:users,nik,'.$user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'],
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}
