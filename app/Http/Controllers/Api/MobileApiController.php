<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kantin;
use App\Models\RekeningKantin;
use App\Models\DebitAccount;
use App\Models\Biometric;
use App\Models\Menu;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class MobileApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        $extra = [];
        if ($user->role === 'kantin') {
            $kantin = Kantin::where('user_id', $user->id)->first();
            $extra['kantin'] = $kantin;
        } elseif ($user->role === 'mahasiswa') {
            $extra['debit'] = DebitAccount::where('user_id', $user->id)->first();
            $extra['biometric'] = Biometric::where('user_id', $user->id)->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'photo' => $user->photo,
            ],
            'extra' => $extra
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:kantin,mahasiswa',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'photo' => $photoPath,
        ]);

        if ($request->role === 'kantin') {
            $namaKantin = $request->input('nama_kantin', 'Stand ' . $request->name);
            $noStand = $request->input('no_stand', 'Stand #01');
            Kantin::create([
                'user_id' => $user->id,
                'nama_kantin' => $namaKantin,
                'no_stand' => $noStand,
                'status_verifikasi' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan masuk.',
            'user' => $user
        ]);
    }

    public function adminKantinList()
    {
        $kantins = Kantin::with(['user', 'rekening'])->get();
        return response()->json([
            'success' => true,
            'data' => $kantins
        ]);
    }

    public function adminApproveKantin($id)
    {
        $kantin = Kantin::findOrFail($id);
        $kantin->status_verifikasi = 'approved';
        $kantin->save();

        return response()->json([
            'success' => true,
            'message' => 'Stand kantin ' . $kantin->nama_kantin . ' berhasil disetujui (Approved)!'
        ]);
    }

    public function adminMahasiswaList()
    {
        $mahasiswas = User::where('role', 'mahasiswa')
            ->with(['debitAccount', 'biometric'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mahasiswas
        ]);
    }

    public function adminResetBio($userId)
    {
        Biometric::where('user_id', $userId)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data sidik jari biometrik berhasil direset.'
        ]);
    }

    public function kantinDashboard(Request $request)
    {
        $userId = $request->input('user_id');
        $kantin = Kantin::where('user_id', $userId)->first();

        if (!$kantin) {
            return response()->json([
                'success' => false,
                'message' => 'Data stand kantin tidak ditemukan.'
            ], 404);
        }

        $totalRevenue = Transaction::where('kantin_id', $kantin->id)
            ->where('status', 'success')
            ->sum('total_amount');

        $totalTransactions = Transaction::where('kantin_id', $kantin->id)
            ->where('status', 'success')
            ->count();

        $recentTransactions = Transaction::where('kantin_id', $kantin->id)
            ->with('student')
            ->latest()
            ->take(15)
            ->get();

        return response()->json([
            'success' => true,
            'kantin' => $kantin,
            'summary' => [
                'total_revenue' => (int) $totalRevenue,
                'total_transactions' => $totalTransactions,
                'status_verifikasi' => $kantin->status_verifikasi
            ],
            'recent_transactions' => $recentTransactions
        ]);
    }

    public function kantinMenuList(Request $request)
    {
        $userId = $request->input('user_id');
        $kantin = Kantin::where('user_id', $userId)->first();
        $kantinId = $kantin ? $kantin->id : 0;

        $menus = Menu::where('kantin_id', $kantinId)->get();

        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    public function kantinStoreMenu(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'kategori' => 'nullable|in:makanan,minuman',
        ]);

        $kantin = Kantin::where('user_id', $request->user_id)->first();
        if (!$kantin) {
            return response()->json(['success' => false, 'message' => 'Kantin tidak ditemukan.'], 404);
        }

        $data = [
            'kantin_id' => $kantin->id,
            'nama_menu' => $request->nama_menu,
            'kategori' => $request->kategori ?? 'makanan',
            'harga' => $request->harga,
            'stock' => $request->stock,
        ];

        if ($request->hasFile('foto_menu')) {
            $data['foto_menu'] = $request->file('foto_menu')->store('menu_photos', 'public');
        } elseif ($request->filled('foto_url')) {
            $data['foto_menu'] = $request->foto_url;
        }

        $menu = Menu::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Menu baru berhasil ditambahkan!',
            'data' => $menu
        ]);
    }

    public function kantinUpdateMenu(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = [
            'nama_menu' => $request->input('nama_menu', $menu->nama_menu),
            'kategori' => $request->input('kategori', $menu->kategori),
            'harga' => $request->input('harga', $menu->harga),
            'stock' => $request->input('stock', $menu->stock),
        ];

        if ($request->hasFile('foto_menu')) {
            $data['foto_menu'] = $request->file('foto_menu')->store('menu_photos', 'public');
        } elseif ($request->filled('foto_url')) {
            $data['foto_url'] = $request->foto_url;
        }

        $menu->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui!',
            'data' => $menu
        ]);
    }

    public function kantinDeleteMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus.'
        ]);
    }

    public function kantinGetRekening(Request $request)
    {
        $userId = $request->input('user_id');
        $kantin = Kantin::where('user_id', $userId)->first();
        $rekening = RekeningKantin::where('kantin_id', $kantin ? $kantin->id : 0)->first();

        return response()->json([
            'success' => true,
            'kantin' => $kantin,
            'rekening' => $rekening
        ]);
    }

    public function kantinStoreRekening(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder_name' => 'required|string',
        ]);

        $kantin = Kantin::where('user_id', $request->user_id)->first();
        if (!$kantin) {
            return response()->json(['success' => false, 'message' => 'Kantin tidak ditemukan.'], 404);
        }

        $rekening = RekeningKantin::updateOrCreate(
            ['kantin_id' => $kantin->id],
            [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Rekening pencairan berhasil disimpan!',
            'data' => $rekening
        ]);
    }

    public function mahasiswaDashboard(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        $debit = DebitAccount::where('user_id', $userId)->first();
        $biometric = Biometric::where('user_id', $userId)->first();
        $recentTransactions = Transaction::where('student_id', $userId)
            ->with(['kantin', 'details.menu'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'user' => $user,
            'debit' => $debit,
            'biometric' => $biometric,
            'recent_transactions' => $recentTransactions
        ]);
    }

    public function mahasiswaStorePembayaran(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'bank_name' => 'required|string',
        ]);

        $cardNumber = preg_replace('/\D/', '', $request->input('card_number', ''));
        $maskedCard = $request->input('card_token_masked');

        if (!empty($cardNumber)) {
            $prefix = substr($cardNumber, 0, 4);
            $suffix = substr($cardNumber, -4);
            $maskedCard = $prefix . ' ' . substr($cardNumber, 4, 2) . '** **** ' . $suffix;
        }

        if (empty($maskedCard)) {
            $maskedCard = '4811 11** **** ' . rand(1000, 9999);
        }

        $cardToken = 'tok_midtrans_' . strtolower(str_replace(' ', '', $request->bank_name)) . '_' . Str::random(12);

        $debit = DebitAccount::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'bank_name' => $request->bank_name,
                'card_token_masked' => $maskedCard,
                'card_token' => $cardToken,
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Kartu Debit ' . $request->bank_name . ' berhasil ditautkan!',
            'data' => $debit
        ]);
    }

    public function mahasiswaStoreBiometric(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan'], 404);
        }

        $action = $request->input('action');

        // 1. ACTION: start_enrollment (Memicu ESP32 menyalakan sensor scanner)
        if ($action === 'start_enrollment') {
            $biometric = Biometric::where('user_id', $user->id)->first();
            if ($biometric && $biometric->fingerprint_id > 0) {
                $targetSlot = $biometric->fingerprint_id;
            } else {
                $usedSlots = Biometric::pluck('fingerprint_id')->toArray();
                $targetSlot = 1;
                while (in_array($targetSlot, $usedSlots) && $targetSlot <= 127) {
                    $targetSlot++;
                }
                if ($targetSlot > 127) {
                    return response()->json([
                        'success' => false,
                        'is_full' => true,
                        'message' => 'Kapasitas slot sensor pemindai telah penuh (Maksimal 127 pengguna).'
                    ], 400);
                }
            }

            $sessionData = [
                'status' => 'waiting_device',
                'user_id' => $user->id,
                'name' => $user->name,
                'slot_id' => $targetSlot,
                'created_at' => now()->timestamp,
            ];

            Cache::put('biometric_enroll_session', $sessionData, 120);

            return response()->json([
                'success' => true,
                'status' => 'waiting_device',
                'slot_id' => $targetSlot,
                'message' => 'Silakan tempelkan jari Anda pada sensor scanner alat kasir.',
                'session' => $sessionData,
            ]);
        }

        // 2. ACTION: check_status (Polling status pendaftaran dari Android)
        if ($action === 'check_status') {
            $session = Cache::get('biometric_enroll_session');
            $biometric = Biometric::where('user_id', $user->id)->first();

            if ($session) {
                if ($session['status'] === 'success') {
                    return response()->json([
                        'success' => true,
                        'status' => 'success',
                        'biometric_enrolled' => true,
                        'fingerprint_id' => $session['slot_id'] ?? ($biometric->fingerprint_id ?? 1),
                        'message' => 'Sidik jari berhasil direkam dan aktif!'
                    ]);
                }

                if ($session['status'] === 'duplicate') {
                    return response()->json([
                        'success' => false,
                        'status' => 'failed',
                        'is_duplicate' => true,
                        'message' => $session['message'] ?? 'Sidik jari ini sudah terdaftar pada akun lain!'
                    ]);
                }

                if ($session['status'] === 'scanning') {
                    $isSecond = ($session['step'] ?? '') === 'step2_wait';
                    return response()->json([
                        'success' => true,
                        'status' => $isSecond ? 'waiting_finger_2' : 'scanning',
                        'step' => $session['step'] ?? 'step1_wait',
                        'attempt' => $session['attempt'] ?? 1,
                        'message' => $isSecond ? 'Tempelkan jari sekali lagi untuk konfirmasi' : 'Sensor sedang membaca sidik jari...'
                    ]);
                }

                if ($session['status'] === 'failed') {
                    return response()->json([
                        'success' => false,
                        'status' => 'failed',
                        'message' => $session['message'] ?? 'Perekaman sidik jari gagal atau timeout.'
                    ]);
                }
            }

            if ($biometric) {
                return response()->json([
                    'success' => true,
                    'status' => 'success',
                    'biometric_enrolled' => true,
                    'fingerprint_id' => $biometric->fingerprint_id,
                    'message' => 'Sidik jari aktif'
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => 'waiting_device',
                'biometric_enrolled' => false,
                'message' => 'Menunggu pemindai...'
            ]);
        }

        // 3. Fallback: Direct fingerprint_id store
        if ($request->filled('fingerprint_id')) {
            $bio = Biometric::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'fingerprint_id' => $request->fingerprint_id,
                    'fingerprint_hash' => 'FP-' . $request->fingerprint_id . '-' . strtoupper(Str::random(8)),
                    'registered_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Sidik jari AS608 (Slot #' . $request->fingerprint_id . ') berhasil didaftarkan!',
                'data' => $bio
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Parameter tidak valid. Gunakan action=start_enrollment atau action=check_status.'
        ], 400);
    }

    public function mahasiswaRiwayat(Request $request)
    {
        $userId = $request->input('user_id');
        $transactions = Transaction::where('student_id', $userId)
            ->with(['kantin', 'details.menu'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}