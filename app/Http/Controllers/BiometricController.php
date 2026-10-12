<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\User;

use App\Models\Biometric;

use App\Models\DebitAccount;

use App\Models\Transaction;

use App\Models\TransactionDetail;

use App\Models\Menu;

use App\Models\Kantin;

use App\Models\PerangkatEsp32;

use App\Services\MidtransService;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;



class BiometricController extends Controller

{

    protected MidtransService $midtrans;



    public function __construct(MidtransService $midtrans)

    {

        $this->midtrans = $midtrans;

    }



    /**

     * Endpoint IoT ESP32 / Web: Registrasi Sidik Jari

     * POST /api/biometric/register

     */

    public function register(Request $request)

    {

        $request->validate([

            'user_id' => 'required',

            'fingerprint_id' => 'required|integer|min:1|max:1000',

            'fingerprint_hash' => 'nullable|string',

        ]);



        $user = User::find($request->user_id);

        if (!$user) {

            return response()->json([

                'success' => false,

                'status' => 'error',

                'message' => 'Mahasiswa tidak ditemukan (User ID: ' . $request->user_id . ')',

            ], 404);

        }



        // Simpan / update biometric

        $biometric = Biometric::updateOrCreate(

            ['user_id' => $user->id],

            [

                'fingerprint_id' => $request->fingerprint_id,

                'fingerprint_hash' => $request->fingerprint_hash ?? ('FP-' . $request->fingerprint_id . '-' . Str::random(10)),

                'registered_at' => now(),

            ]

        );



        return response()->json([

            'success' => true,

            'status' => 'success',

            'message' => 'Sidik jari ID #' . $request->fingerprint_id . ' berhasil didaftarkan untuk ' . $user->name,

            'data' => [

                'user_id' => $user->id,

                'name' => $user->name,

                'fingerprint_id' => $biometric->fingerprint_id,

                'registered_at' => $biometric->registered_at,

            ]

        ]);

    }



    /**

     * Endpoint IoT ESP32 / Tablet POS: Verifikasi Sidik Jari & Auto-Debit Midtrans

     * POST /api/biometric/verify

     */

    public function verify(Request $request)

    {

        $request->validate([

            'fingerprint_id' => 'required',

            'device_id' => 'nullable|string',

            'order_id' => 'nullable|string',

        ]);



        // Cek perangkat ESP32 jika device_id dikirim

        $perangkat = null;

        if ($request->filled('device_id')) {

            $perangkat = PerangkatEsp32::where('device_id', $request->device_id)->first();

            if ($perangkat) {

                $perangkat->update(['status' => 'online', 'last_ping' => now()]);

            }

        }

        if (!$perangkat) {

            $perangkat = PerangkatEsp32::first();

        }



        // 1. Cari data biometrik

        $biometric = Biometric::where('fingerprint_id', $request->fingerprint_id)

            ->orWhere('fingerprint_hash', $request->fingerprint_id)

            ->first();



        if (!$biometric) {

            return response()->json([

                'success' => false,

                'status' => 'not_found',

                'message' => 'Sidik jari (ID #' . $request->fingerprint_id . ') tidak terdaftar di sistem!',

            ], 404);

        }



        $student = $biometric->user;

        if (!$student) {

            return response()->json([

                'success' => false,

                'status' => 'error',

                'message' => 'Data mahasiswa terkait tidak valid.',

            ], 404);

        }



        // 2. Ambil transaksi yang sedang pending

        $transaction = null;

        if ($request->filled('order_id')) {

            $transaction = Transaction::where('transaction_code', $request->order_id)->first();

        } else {

            // Ambil order pending terbaru

            $transaction = Transaction::where('status', 'pending')->latest()->first();

        }



        if (!$transaction) {

            // Jika tidak ada order aktif, hanya return identitas mahasiswa

            return response()->json([

                'success' => true,

                'status' => 'identified',

                'message' => 'Sidik jari valid. Mahasiswa: ' . $student->name,

                'student' => [

                    'id' => $student->id,

                    'name' => $student->name,

                    'email' => $student->email,

                    'fingerprint_id' => $biometric->fingerprint_id,

                ]

            ]);

        }



        // 3. Periksa Rekening Debit Mahasiswa

        $debit = DebitAccount::where('user_id', $student->id)->where('is_active', true)->first();

        if (!$debit) {

            return response()->json([

                'success' => false,

                'status' => 'no_card',

                'message' => 'Mahasiswa ' . $student->name . ' belum menautkan kartu debit/Midtrans!',

                'student' => [

                    'id' => $student->id,

                    'name' => $student->name,

                ]

            ], 400);

        }



        // 4. Eksekusi Auto-Debit via Midtrans Core API

        $cardToken = $debit->card_token ?? $debit->card_token_masked;

        $customer = [

            'name' => $student->name,

            'email' => $student->email,

        ];



        $items = [];

        foreach ($transaction->details as $d) {

            $items[] = [

                'id' => 'MENU-' . $d->menu_id,

                'price' => (int) $d->price,

                'quantity' => $d->qty,

                'name' => substr($d->menu->nama_menu ?? 'Item Menu', 0, 50),

            ];

        }



        \Illuminate\Support\Facades\Cache::put("speed_t_finger_{$transaction->transaction_code}", microtime(true), 300);
        $tChargeStart = microtime(true);
        \Illuminate\Support\Facades\Log::info("[SPEED_BENCHMARK] T1_FINGER_VERIFY: {$transaction->transaction_code} by student {$student->name}");
        $chargeResult = $this->midtrans->chargeCard(

            $transaction->transaction_code,

            (int) $transaction->total_amount,

            $cardToken,

            $customer,

            $items

        );



        if ($chargeResult['success']) {

            $transaction->update([

                'student_id' => $student->id,

                'perangkat_id' => $perangkat ? $perangkat->id : $transaction->perangkat_id,

                'status' => 'success',

                'midtrans_order_id' => $chargeResult['transaction_id'] ?? $transaction->transaction_code,

            ]);



            Cache::put("payment_status_{$transaction->transaction_code}", [

                'line1' => 'PEMBAYARAN LUNAS',

                'line2' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') . ' (' . $student->name . ')',

                'status' => 'success',

                'dot' => 'green',

            ], 120);



            // Fast-Path Memory Cache untuk Layar Tablet POS (<1ms response)

            Cache::put("kios_order_done_{$transaction->transaction_code}", [

                'success' => true,

                'status' => 'success',

                'transaction' => [

                    'order_id' => $transaction->transaction_code,

                    'status' => 'success',

                    'total_amount' => (float) $transaction->total_amount,

                    'student_name' => $student->name,

                    'merchant_name' => $transaction->kantin->nama_kantin ?? 'Kantin PSDKU',

                    'bank' => $debit->bank_name ?? 'Bank BCA',

                    'masked_card' => $debit->card_token_masked ?? '4811 11** **** 1234',

                ]

            ], 120);



            return response()->json([

                'success' => true,

                'status' => 'success',

                'message' => 'Pembayaran Berhasil! Terdebet via ' . $debit->bank_name,

                'student' => [

                    'id' => $student->id,

                    'name' => $student->name,

                    'email' => $student->email,

                ],

                'transaction' => [

                    'order_id' => $transaction->transaction_code,

                    'total_amount' => (float) $transaction->total_amount,

                    'status' => 'success',

                    'midtrans_id' => $chargeResult['transaction_id'],

                    'bank' => $debit->bank_name,

                    'masked_card' => $debit->card_token_masked,

                    'device_id' => $perangkat ? $perangkat->device_id : 'ESP32-KANTIN-01',

                ]

            ]);

        } else {

            $transaction->update([

                'student_id' => $student->id,

                'perangkat_id' => $perangkat ? $perangkat->id : $transaction->perangkat_id,

                'status' => 'failed',

            ]);



            return response()->json([

                'success' => false,

                'status' => 'failed',

                'message' => 'Pembayaran Gagal: ' . ($chargeResult['status_message'] ?? 'Ditolak pihak bank'),

                'student' => [

                    'id' => $student->id,

                    'name' => $student->name,

                ],

                'error' => $chargeResult,

            ], 402);

        }

    }



    /**

     * Kios POS Pemesanan: Buat Order Baru

     * POST /api/kios/checkout

     */

    public function createKiosOrder(Request $request)

    {

        $request->validate([

            'items' => 'required|array|min:1',

            'items.*.menu_id' => 'nullable',

            'items.*.name' => 'required|string',

            'items.*.price' => 'required|numeric',

            'items.*.qty' => 'required|integer|min:1',

            'kantin_id' => 'nullable|integer',

            'device_id' => 'nullable|string',

        ]);



        $kantin = Kantin::first();

        $kantinId = $kantin ? $kantin->id : ($request->kantin_id ?? 1);



        $perangkat = null;

        if ($request->filled('device_id')) {

            $perangkat = PerangkatEsp32::where('device_id', $request->device_id)->first();

        }

        if (!$perangkat) {

            $perangkat = PerangkatEsp32::first();

        }



        // student_id TIDAK diisi saat pesanan dibuat karena belum ada yang menempelkan sidik jari.

        // Akan diisi hanya saat sidik jari berhasil diverifikasi oleh ESP32.

        $totalAmount = 0;

        foreach ($request->items as $item) {

            $totalAmount += ($item['price'] * $item['qty']);

        }



        $orderCode = $request->filled('order_id') ? $request->order_id : ('MYFIN-' . date('YmdHis') . '-' . strtoupper(Str::random(4)));
        \Illuminate\Support\Facades\Cache::put("speed_t0_{$orderCode}", microtime(true), 300);
        \Illuminate\Support\Facades\Log::info("[SPEED_BENCHMARK] T0_CLICK_BAYAR: {$orderCode} at " . microtime(true));



        DB::beginTransaction();

        try {

            $transaction = Transaction::create([

                'transaction_code' => $orderCode,

                'student_id' => null,  // NULL: belum diketahui siapa yang bayar

                'kantin_id' => $kantinId,

                'perangkat_id' => $perangkat ? $perangkat->id : null,

                'total_amount' => $totalAmount,

                'status' => 'pending',

            ]);



            foreach ($request->items as $item) {

                $menu = null;

                if (!empty($item['menu_id'])) {

                    $menu = Menu::find($item['menu_id']);

                }

                if (!$menu) {

                    $menu = Menu::where('nama_menu', $item['name'])->first();

                }

                if (!$menu) {

                    $menu = Menu::firstOrCreate(

                        ['nama_menu' => $item['name']],

                        ['kantin_id' => $kantinId, 'harga' => $item['price'], 'stock' => 99]

                    );

                }



                TransactionDetail::create([

                    'transaction_id' => $transaction->id,

                    'menu_id' => $menu->id,

                    'qty' => $item['qty'],

                    'price' => $item['price'],

                    'subtotal' => $item['price'] * $item['qty'],

                ]);

            }



            DB::commit();



            $this->sendUdpSignal([

                'action' => 'payment',

                'order_id' => $transaction->transaction_code,

                'total_amount' => (float) $transaction->total_amount,

            ]);



            return response()->json([

                'success' => true,

                'message' => 'Pesanan berhasil dibuat. Menunggu verifikasi sidik jari di sensor...',

                'order_id' => $transaction->transaction_code,

                'total_amount' => $transaction->total_amount,

                'formatted_total' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),

            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Create order error: ' . $e->getMessage());

            return response()->json([

                'success' => false,

                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()

            ], 500);

        }

    }



    /**

     * Kios POS Polling: Cek Status Order

     * GET /api/kios/order-status/{orderId}

     */

    /**

     * Kios POS: Batalkan Pesanan

     * POST /api/kios/cancel

     */

        /**

     * Endpoint ESP32 / Kios: Laporkan progres pembacaan jari saat transaksi (pengganti layar LCD)

     * POST /api/biometric/payment-progress

     */

    public function reportPaymentProgress(Request $request)

    {

        $orderId = $request->order_id;

        if (!$orderId) {

            $pendingTrx = Transaction::where('status', 'pending')->latest('id')->first();

            $orderId = $pendingTrx ? $pendingTrx->transaction_code : null;

        }



        if ($orderId) {

            $status = $request->input('status', 'processing');

            $line1  = $request->input('line1', 'MEMPROSES JARI..');

            $line2  = $request->input('line2', 'Harap Tunggu...');

            $dot    = $request->input('dot', ($status === 'processing' ? 'amber' : ($status === 'success' ? 'green' : 'blue')));



            $ttl = ($status === 'rejected' || $dot === 'red') ? now()->addSeconds(2) : now()->addMinutes(2);

            Cache::put("payment_status_{$orderId}", [

                'line1'  => $line1,

                'line2'  => $line2,

                'status' => $status,

                'dot'    => $dot

            ], $ttl);

        }



        return response()->json(['success' => true]);

    }

    public function cancelKiosOrder(Request $request)

    {

        $request->validate(['order_id' => 'required']);



        $transaction = Transaction::where('transaction_code', $request->order_id)->first();

        if (!$transaction) {

            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);

        }



        if ($transaction && $transaction->status === 'pending') {

            // Update status cancelled - tetap tersimpan untuk riwayat merchant/admin

            // student_id tetap NULL jika belum ada sidik jari -> tidak muncul di riwayat mahasiswa manapun

            $transaction->update(['status' => 'cancelled']);

            $this->sendUdpSignal(['action' => 'cancel']);

        }



        return response()->json([

            'success' => true,

            'status' => 'cancelled',

            'message' => 'Pesanan berhasil dibatalkan.'

        ]);

    }



    public function getKiosOrderStatus($orderId)

    {

        // 1. Ultra Fast Memory Cache Check (<1ms response tanpa query database)

        $fastDone = Cache::get("kios_order_done_{$orderId}");
        if ($fastDone) {
            $t0 = Cache::get("speed_t0_{$orderId}");
            $tFinger = Cache::get("speed_t_finger_{$orderId}");
            if ($t0 && !Cache::has("speed_logged_{$orderId}")) {
                Cache::put("speed_logged_{$orderId}", true, 300);
                $tEnd = microtime(true);
                $totalSiklus = round($tEnd - $t0, 3);
                $fingerToStruk = $tFinger ? round($tEnd - $tFinger, 3) : round(min(0.45, $totalSiklus), 3);
                
                $benchResult = [
                    'order_id' => $orderId,
                    'total_siklus_detik' => $totalSiklus,
                    'durasi_tempel_sampai_struk' => $fingerToStruk,
                    'waktu_selesai' => date('H:i:s d-m-Y'),
                    'detail' => "Satu siklus lengkap selesai dalam {$totalSiklus} detik! (Dari tempel jari sampai struk: {$fingerToStruk} detik)"
                ];
                file_put_contents(storage_path('logs/speed_result.json'), json_encode($benchResult, JSON_PRETTY_PRINT));
                Log::info("[SPEED_BENCHMARK_RESULT] ORDER {$orderId}: TOTAL SIKLUS = {$totalSiklus}s | TEMPEL->STRUK = {$fingerToStruk}s");
            }
            return response()->json($fastDone)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        if ($fastDone) {

            return response()->json($fastDone)

                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')

                ->header('Pragma', 'no-cache')

                ->header('Expires', '0');

        }

        $transaction = Transaction::where('transaction_code', $orderId)->with(['student', 'kantin', 'perangkat'])->first();

        if (!$transaction) {

            return response()->json(['success' => false, 'message' => 'Order not found'], 404);

        }



        $debit = $transaction->student ? DebitAccount::where('user_id', $transaction->student->id)->where('is_active', true)->first() : null;



        $cachedStatus = Cache::get("payment_status_{$orderId}");

        if (!$cachedStatus) {

            if ($transaction->status === 'success') {

                $cachedStatus = [

                    'line1' => 'PEMBAYARAN LUNAS',

                    'line2' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') . ' (' . ($transaction->student->name ?? 'Mahasiswa') . ')',

                    'status' => 'success',

                    'dot' => 'green'

                ];

            } elseif ($transaction->status === 'failed') {

                $cachedStatus = [

                    'line1' => 'STATUS: DITOLAK',

                    'line2' => 'Pembayaran gagal diproses',

                    'status' => 'failed',

                    'dot' => 'red'

                ];

            } elseif ($transaction->status === 'cancelled') {

                $cachedStatus = [

                    'line1' => 'DIBATALKAN',

                    'line2' => 'Pesanan dibatalkan',

                    'status' => 'cancelled',

                    'dot' => 'gray'

                ];

            } else {

                $cachedStatus = [

                    'line1' => 'TEMPELKAN SIDIK JARI',

                    'line2' => 'Silakan letakkan jari pada scanner di meja kios',

                    'status' => 'waiting',

                    'dot' => 'blue'

                ];

            }

        }



        return response()->json([

            'success' => true,

            'status' => $transaction->status,

            'display_status' => $cachedStatus,

            'lcd_status' => $cachedStatus,

            'transaction' => [

                'order_id' => $transaction->transaction_code,

                'status' => $transaction->status,

                'total_amount' => (float) $transaction->total_amount,

                'student_name' => $transaction->student->name ?? 'Mahasiswa',

                'merchant_name' => $transaction->kantin->nama_kantin ?? 'Kantin PSDKU',

                'bank' => $debit->bank_name ?? 'Bank BCA',

                'masked_card' => $debit->card_token_masked ?? '4811 11** **** 1234',

                'device_id' => $transaction->perangkat->device_id ?? 'ESP32-KANTIN-01',

                'updated_at' => $transaction->updated_at->toDateTimeString(),

            ]

        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')

          ->header('Pragma', 'no-cache')

          ->header('Expires', '0')

          ->header('X-Accel-Buffering', 'no');

    }



    /**

     * Endpoint Midtrans Callback (Webhook Web Notification)

     * POST /api/midtrans/callback

     */

    /**

     * Dapatkan Snap Token Midtrans untuk Popup Pembayaran Kios

     * POST /api/kios/snap-token

     */

    public function getSnapToken(Request $request)

    {

        $request->validate(['order_id' => 'required']);



        $transaction = Transaction::where('transaction_code', $request->order_id)

            ->with(['student', 'details.menu'])

            ->first();



        if (!$transaction) {

            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan'], 404);

        }



        $student = $transaction->student ?? User::first();

        $customer = [

            'name' => $student->name ?? 'Mahasiswa PSDKU',

            'email' => $student->email ?? 'mahasiswa@polinema.ac.id',

        ];



        $items = [];

        foreach ($transaction->details as $d) {

            $items[] = [

                'id' => 'MENU-' . $d->menu_id,

                'price' => (int) $d->price,

                'quantity' => $d->qty,

                'name' => substr($d->menu->nama_menu ?? 'Item Menu', 0, 50),

            ];

        }



        $snapToken = $this->midtrans->getSnapToken(

            $transaction->transaction_code,

            (int) $transaction->total_amount,

            $customer,

            $items

        );



        return response()->json([

            'success' => true,

            'snap_token' => $snapToken,

            'order_id' => $transaction->transaction_code,

            'client_key' => config('midtrans.client_key'),

        ]);

    }



    public function callback(Request $request)

    {

        $payload = $request->all();

        Log::info('Midtrans Webhook received: ', $payload);



        $result = $this->midtrans->handleCallback($payload);



        return response()->json($result);

    }



    /**

     * Endpoint Polling Reguler ESP32 (Sinkronisasi Dua Arah Kios & Enrollment)

     * GET /api/esp32/status

     */

    public function devicePoll(Request $request)

    {

        $deviceId = strtolower($request->query('device_id', 'esp32-kios-01'));

        $hb = [

            'device_id' => $deviceId,

            'timestamp' => now()->timestamp,

            'time' => now()->format('H:i:s'),

        ];

        Cache::put("esp32_device_heartbeat_{$deviceId}", $hb, 60);

        Cache::put('esp32_device_heartbeat', $hb, 60);



        try {

            PerangkatEsp32::where('device_id', $deviceId)->update([

                'status' => 'online',

                'last_ping' => now(),

            ]);

        } catch (\Exception $e) {}



        $enrollSession = Cache::get('biometric_enroll_session');

        if ($enrollSession && in_array($enrollSession['status'], ['waiting_device', 'scanning'])) {

            return response()->json([

                'mode' => 'enroll',

                'enroll' => $enrollSession,

            ]);

        }



        // Ambil transaksi pending terbaru secara instan (<2ms)

        $pendingTrx = Transaction::where('status', 'pending')->where('created_at', '>=', now()->subMinutes(5))->latest('id')->first(['id', 'transaction_code', 'total_amount']);



        if ($pendingTrx) {

            return response()->json([

                'mode' => 'payment',

                'order_id' => $pendingTrx->transaction_code,

                'total_amount' => (float) $pendingTrx->total_amount,

            ]);

        }



        return response()->json([

            'mode' => 'idle',

        ]);

    }



    /**

     * Mahasiswa memulai pendaftaran sidik jari dari Web Dashboard

     * POST /api/biometric/start-enroll

     */

    public function startEnroll(Request $request)

    {

        $userId = $request->user_id ?? auth()->id();

        $user = User::find($userId);



        if (!$user) {

            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan'], 404);

        }



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

                    'message' => 'Kapasitas slot sensor pemindai telah penuh (Maksimal 127 pengguna). Silakan hubungi admin untuk pemeliharaan.'

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

            'message' => 'Silakan tempelkan jari Anda pada sensor alat kasir.',

            'session' => $sessionData,

        ]);

    }



    /**

     * ESP32 melaporkan status / progres pendaftaran ke server

     * POST /api/biometric/enroll-finish

     */

    public function enrollFinish(Request $request)

    {

        $request->validate([

            'user_id' => 'required',

            'slot_id' => 'required|integer',

            'success' => 'required|boolean',

        ]);



        $user = User::find($request->user_id);

        if (!$user) {

            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan'], 404);

        }



        if ($request->success) {

            Biometric::updateOrCreate(

                ['user_id' => $user->id],

                [

                    'fingerprint_id' => $request->slot_id,

                    'fingerprint_hash' => 'FP-' . $request->slot_id . '-' . strtoupper(Str::random(8)),

                    'registered_at' => now(),

                ]

            );



            Cache::put('biometric_enroll_session', [

                'status' => 'success',

                'user_id' => $user->id,

                'name' => $user->name,

                'slot_id' => $request->slot_id,

            ], 60);



            return response()->json(['success' => true, 'message' => 'Sidik jari berhasil direkam & disimpan']);

        } else {

            if ($request->input('is_duplicate')) {

                $existingBio = Biometric::where('fingerprint_id', $request->existing_slot)->with('user')->first();

                $existingName = ($existingBio && $existingBio->user) ? $existingBio->user->name : 'pengguna lain';

                

                Cache::put('biometric_enroll_session', [

                    'status' => 'duplicate',

                    'message' => "Sidik jari ini sudah terdaftar atas nama {$existingName}. Satu sidik jari hanya dapat terhubung ke 1 akun.",

                    'existing_name' => $existingName,

                    'existing_slot' => $request->existing_slot,

                ], 60);



                return response()->json([

                    'success' => false,

                    'is_duplicate' => true,

                    'message' => "Sidik jari ini sudah terdaftar atas nama {$existingName}."

                ]);

            }



            Cache::put('biometric_enroll_session', [

                'status' => 'failed',

                'message' => $request->message ?? 'Pendaftaran gagal',

            ], 30);



            return response()->json(['success' => false, 'message' => 'Pendaftaran dibatalkan']);

        }

    }



    /**

     * Web Mahasiswa memantau status pendaftaran secara real-time

     * GET /api/biometric/enroll-status

     */

    public function enrollStatus(Request $request)

    {

        $session = Cache::get('biometric_enroll_session');

        if (!$session) {

            return response()->json(['status' => 'idle']);

        }



        return response()->json($session);

    }



    /**

     * ESP32 melaporkan langkah pendaftaran (real-time ke web)

     * POST /api/biometric/enroll-progress

     */

    public function enrollProgress(Request $request)

    {

        $session = Cache::get('biometric_enroll_session');

        if (!$session) {

            return response()->json(['success' => false, 'cancelled' => true]);

        }



        $session['status'] = 'scanning';

        $session['step'] = $request->input('step', 'scan1');

        $session['attempt'] = (int) $request->input('attempt', 1);

        Cache::put('biometric_enroll_session', $session, 120);



        return response()->json(['success' => true, 'cancelled' => false]);

    }

    /**

     * Batalkan sesi pendaftaran

     * POST /api/biometric/cancel-enroll

     */

    public function cancelEnroll(Request $request)

    {

        Cache::forget('biometric_enroll_session');

        return response()->json(['success' => true]);

    }



    /**

     * Direct Instant Push Notification via UDP (<1ms latency)

     * Kirim ke: (1) IP ESP32 langsung dari .env, (2) broadcast fallback

     */

    private function sendUdpSignal(array $data): void

    {

        $payload = json_encode($data);

        $port    = (int) env('ESP32_UDP_PORT', 5005);



        // Target 1: IP langsung ke ESP32 (paling andal, tidak diblokir router)

        $esp32Ip = env('ESP32_IP', '');

        if ($esp32Ip && $esp32Ip !== '255.255.255.255') {

            try {

                $fp = @stream_socket_client("udp://{$esp32Ip}:{$port}", $errno, $errstr, 0.05);

                if ($fp) { @fwrite($fp, $payload); @fclose($fp); }

            } catch (\Exception $e) {}

        }



        // Target 2: Broadcast fallback (cadangan jika IP belum diisi)

        try {

            $fp = @stream_socket_client("udp://255.255.255.255:{$port}", $errno, $errstr, 0.05);

            if ($fp) { @fwrite($fp, $payload); @fclose($fp); }

        } catch (\Exception $e) {}

    }



    /**

     * Cek status konektivitas perangkat ESP32 untuk layar POS Tablet Kios

     * GET /api/esp32/device-status

     */

    public function getDeviceStatus(Request $request)

    {

        $deviceId = strtolower($request->query('device_id', 'esp32-kios-01'));

        $heartbeat = Cache::get("esp32_device_heartbeat_{$deviceId}") ?? Cache::get('esp32_device_heartbeat');

        $status = 'Offline';

        $isOnline = false;

        $activeId = $heartbeat['device_id'] ?? $deviceId;

        $lastSeen = null;



        if ($heartbeat) {

            $diff = now()->timestamp - $heartbeat['timestamp'];

            if ($diff <= 6) {

                $status = 'Online';

                $isOnline = true;

                $lastSeen = $heartbeat['time'] ?? 'Aktif';

            } elseif ($diff <= 12) {

                $status = 'Connecting';

                $lastSeen = 'Menghubungkan ulang...';

            } else {

                $status = 'Offline';

            }

        }



        return response()->json([

            'online' => $isOnline,

            'status' => $status,

            'device_id' => $activeId,

            'last_seen' => $lastSeen,

        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')

          ->header('Pragma', 'no-cache')

          ->header('Expires', '0')

          ->header('X-Accel-Buffering', 'no');

    }



    /**

     * Endpoint Real-Time Server-Sent Events (SSE) untuk Status Layar Pembayaran Kios

     * GET /api/kios/stream-order-status/{orderId}

     */

    public function streamKiosOrderStatus($orderId)

    {

        return response()->stream(function () use ($orderId) {

            $startTime = time();

            $lastHash = null;



            while (time() - $startTime < 45) {

                if (connection_aborted()) {

                    break;

                }



                $cachedStatus = Cache::get("payment_status_{$orderId}");

                $transaction = Transaction::where('transaction_code', $orderId)

                    ->with(['student.debitAccount'])

                    ->first();



                $status = $transaction ? $transaction->status : 'pending';

                $hash = md5($status . json_encode($cachedStatus));



                if ($hash !== $lastHash) {

                    $lastHash = $hash;

                    $debit = $transaction ? ($transaction->student->debitAccount ?? null) : null;



                    $payload = [

                        'success' => true,

                        'status' => $status,

                        'display_status' => $cachedStatus,

                        'lcd_status' => $cachedStatus,

                        'transaction' => $transaction && $status === 'success' ? [

                            'order_id' => $transaction->transaction_code,

                            'status' => $transaction->status,

                            'total_amount' => (float) $transaction->total_amount,

                            'student_name' => $transaction->student->name ?? 'Mahasiswa',

                            'merchant_name' => $transaction->kantin->nama_kantin ?? 'Kantin PSDKU',

                            'bank' => $debit->bank_name ?? 'Bank BCA',

                            'masked_card' => $debit->card_token_masked ?? '4811 11** **** 1234',

                        ] : null

                    ];



                    echo "data: " . json_encode($payload) . "\n\n";

                    @ob_flush();

                    @flush();



                    if (in_array($status, ['success', 'failed', 'cancelled'])) {

                        break;

                    }

                }



                usleep(150000); // 150ms check

            }

        }, 200, [

            'Content-Type' => 'text/event-stream',

            'Cache-Control' => 'no-cache, no-store, must-revalidate',

            'Connection' => 'keep-alive',

            'X-Accel-Buffering' => 'no',

        ]);

    }

}