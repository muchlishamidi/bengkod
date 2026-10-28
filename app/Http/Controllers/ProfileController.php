<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman ganti password.
     */
    public function editPassword()
    {
        $user = Auth::user();
        return view('profile.password', compact('user'));
    }

    /**
     * Proses ubah password dengan validasi Form Request.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Simpan password baru dalam bentuk hash
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('profile.password')
            ->with('success', 'Password akun Anda berhasil diperbarui!');
    }
}
