<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\RekeningKantin;
use App\Models\User;
use App\Models\DebitAccount;
use App\Models\Biometric;

class AdminController extends Controller
{
    // Halaman Approval Kantin
    public function kantinApproval()
    {
        $kantins = Kantin::with('user', 'rekening')->get();
        return view('admin.kantin', compact('kantins'));
    }

    // Halaman Data Mahasiswa
    public function mahasiswaData()
    {
        $mahasiswas = User::where('role', 'mahasiswa')
            ->with('debitAccount', 'biometric')
            ->get();
        return view('admin.mahasiswa', compact('mahasiswas'));
    }

    // Approve kantin
    public function approveKantin($id)
    {
        $kantin = Kantin::findOrFail($id);
        $kantin->status_verifikasi = 'approved';
        $kantin->save();
        return back()->with('success', 'Stand berhasil diverifikasi!');
    }

    // Reset biometrik mahasiswa
    public function resetBiometric($userId)
    {
        Biometric::where('user_id', $userId)->delete();
        return back()->with('success', 'Data sidik jari berhasil di-reset.');
    }
}
