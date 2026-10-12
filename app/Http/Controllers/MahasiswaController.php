<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DebitAccount;
use App\Models\Biometric;
use App\Models\Transaction;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    // 1. Dashboard Utama Mahasiswa
    public function dashboard()
    {
        $user = auth()->user();
        $debit = DebitAccount::where('user_id', $user->id)->first();
        $biometric = Biometric::where('user_id', $user->id)->first();
        $transactions = Transaction::where('student_id', $user->id)
            ->with(['kantin', 'details.menu'])
            ->latest()
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact('user', 'debit', 'biometric', 'transactions'));
    }

    // 2. Metode Pembayaran (Midtrans Auto-Debit)
    public function pembayaran()
    {
        $user = auth()->user();
        $debit = DebitAccount::where('user_id', $user->id)->first();
        $biometric = Biometric::where('user_id', $user->id)->first();

        return view('mahasiswa.pembayaran', compact('user', 'debit', 'biometric'));
    }

    // Simpan/Update Kartu Debit Midtrans
    public function storePembayaran(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'card_number' => 'nullable|string',
            'card_token_masked' => 'nullable|string',
        ]);

        $cardNumber = preg_replace('/\D/', '', $request->card_number ?? '');
        $maskedCard = $request->card_token_masked;

        if (!empty($cardNumber)) {
            $prefix = substr($cardNumber, 0, 4);
            $suffix = substr($cardNumber, -4);
            $maskedCard = $prefix . ' ' . substr($cardNumber, 4, 2) . '** **** ' . $suffix;
        }

        if (empty($maskedCard)) {
            $maskedCard = '4811 11** **** ' . rand(1000, 9999);
        }

        $cardToken = 'tok_midtrans_' . strtolower(str_replace(' ', '', $request->bank_name)) . '_' . Str::random(12);

        DebitAccount::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'bank_name' => $request->bank_name,
                'card_token_masked' => $maskedCard,
                'card_token' => $cardToken,
                'is_active' => true,
            ]
        );

        return redirect()->route('mahasiswa.pembayaran')->with('success', 'Rekening Debit Midtrans berhasil disimpan dan ditautkan!');
    }

    // Simpan/Update Sidik Jari dari Portal Mahasiswa
    public function storeBiometric(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|integer|min:1|max:1000',
        ]);

        $user = auth()->user();

        Biometric::updateOrCreate(
            ['user_id' => $user->id],
            [
                'fingerprint_id' => $request->fingerprint_id,
                'fingerprint_hash' => 'FP-' . $request->fingerprint_id . '-' . strtoupper(Str::random(8)),
                'registered_at' => now(),
            ]
        );

        return back()->with('success', 'Sidik jari sensor AS608 (Slot #' . $request->fingerprint_id . ') berhasil didaftarkan!');
    }

    // 3. Riwayat Belanja Lengkap
    public function riwayat()
    {
        $user = auth()->user();
        $debit = DebitAccount::where('user_id', $user->id)->first();
        $biometric = Biometric::where('user_id', $user->id)->first();
        $transactions = Transaction::where('student_id', $user->id)
            ->with(['kantin', 'details.menu'])
            ->latest()
            ->get();

        return view('mahasiswa.riwayat', compact('user', 'debit', 'biometric', 'transactions'));
    }
}