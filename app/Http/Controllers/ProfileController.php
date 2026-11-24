<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'             => 'required|string|max:50',
            'username'         => ['required', 'alpha_dash', 'min:3', 'max:20',
                                   Rule::unique('users', 'username')->ignore($user->id)],
            'avatar'           => ['required', Rule::in(array_keys(config('avatars')))],
            'current_password' => 'nullable|required_with:password|current_password',
            'password'         => 'nullable|min:6|confirmed',
        ], [
            'name.required'                     => 'Nama wajib diisi.',
            'username.required'                 => 'Username wajib diisi.',
            'username.unique'                   => 'Username ini sudah dipakai.',
            'username.min'                      => 'Username minimal 3 karakter.',
            'username.alpha_dash'               => 'Username hanya boleh huruf, angka, strip, dan garis bawah.',
            'avatar.required'                   => 'Pilih salah satu avatar.',
            'avatar.in'                         => 'Avatar yang dipilih tidak tersedia.',
            'current_password.required_with'    => 'Isi password lama untuk mengganti password.',
            'current_password.current_password' => 'Password lama salah.',
            'password.min'                      => 'Password baru minimal 6 karakter.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Avatar terkunci tidak boleh dipakai sebelum levelnya tercapai
        $avatar = config('avatars.' . $data['avatar']);
        if ($user->level < $avatar['level']) {
            return back()->withInput()->withErrors([
                'avatar' => "Avatar {$avatar['nama']} terbuka di Level {$avatar['level']}.",
            ]);
        }

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->avatar = $data['avatar'];

        if ($request->filled('password')) {
            $user->password = $data['password']; // otomatis di-hash oleh cast di model User
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui ✅');
    }
}