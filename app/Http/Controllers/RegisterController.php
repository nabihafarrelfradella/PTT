<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kantin;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('register');
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:kantin,mahasiswa',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = $request->file('photo')->store('photos', 'public');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'photo' => $photoPath,
        ]);

        // Jika mendaftar sebagai kantin, buat data kantin juga
        if ($request->role === 'kantin') {
            $request->validate([
                'nama_kantin' => 'required|string|max:255',
                'no_stand' => 'required|string|max:100',
            ]);

            Kantin::create([
                'user_id' => $user->id,
                'nama_kantin' => $request->nama_kantin,
                'no_stand' => $request->no_stand,
                'status_verifikasi' => 'pending',
            ]);
        }

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
