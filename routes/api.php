<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiometricController;

/*
|--------------------------------------------------------------------------
| API Routes for MyFin (Midtrans & AS608 IoT Biometric)
|--------------------------------------------------------------------------
*/

// Endpoint IoT ESP32 (Sensor Sidik Jari AS608 & Polling Dua Arah)
Route::get('/esp32/status', [BiometricController::class, 'devicePoll']);
Route::get('/esp32/device-status', [BiometricController::class, 'getDeviceStatus']);
Route::post('/esp32/transaksi', [BiometricController::class, 'verify']);
Route::post('/biometric/register', [BiometricController::class, 'register']);
Route::post('/biometric/verify', [BiometricController::class, 'verify']);

// Endpoint Real-Time Biometric Enrollment (Web <-> ESP32)
Route::post('/biometric/start-enroll', [BiometricController::class, 'startEnroll']);
Route::post('/biometric/enroll-finish', [BiometricController::class, 'enrollFinish']);
Route::post('/biometric/enroll-progress', [BiometricController::class, 'enrollProgress']);
Route::get('/biometric/enroll-status', [BiometricController::class, 'enrollStatus']);
Route::post('/biometric/cancel-enroll', [BiometricController::class, 'cancelEnroll']);

// Endpoint Kios POS Tablet
Route::post('/kios/checkout', [BiometricController::class, 'createKiosOrder']);
Route::post('/kios/cancel', [BiometricController::class, 'cancelKiosOrder']);
Route::get('/kios/order-status/{orderId}', [BiometricController::class, 'getKiosOrderStatus']);
Route::get('/kios/stream-order-status/{orderId}', [BiometricController::class, 'streamKiosOrderStatus']);
Route::post('/kios/snap-token', [BiometricController::class, 'getSnapToken']);
Route::post('/biometric/payment-progress', [BiometricController::class, 'reportPaymentProgress']);

// Endpoint Midtrans Callback (Webhook Web Notification)
Route::post('/midtrans/callback', [BiometricController::class, 'callback']);
// ==========================================
// ROUTES KHUSUS APLIKASI ANDROID (PML UTS)
// ==========================================
use App\Http\Controllers\Api\MobileApiController;

Route::prefix('mobile')->group(function () {
    // Auth
    Route::post('/login', [MobileApiController::class, 'login']);
    Route::post('/register', [MobileApiController::class, 'register']);

    // Admin
    Route::get('/admin/kantin', [MobileApiController::class, 'adminKantinList']);
    Route::post('/admin/kantin/{id}/approve', [MobileApiController::class, 'adminApproveKantin']);
    Route::get('/admin/mahasiswa', [MobileApiController::class, 'adminMahasiswaList']);
    Route::post('/admin/mahasiswa/{id}/reset-bio', [MobileApiController::class, 'adminResetBio']);

    // Kantin (Merchant)
    Route::get('/kantin/dashboard', [MobileApiController::class, 'kantinDashboard']);
    Route::get('/kantin/menu', [MobileApiController::class, 'kantinMenuList']);
    Route::post('/kantin/menu', [MobileApiController::class, 'kantinStoreMenu']);
    Route::post('/kantin/menu/{id}/update', [MobileApiController::class, 'kantinUpdateMenu']);
    Route::delete('/kantin/menu/{id}', [MobileApiController::class, 'kantinDeleteMenu']);
    Route::get('/kantin/rekening', [MobileApiController::class, 'kantinGetRekening']);
    Route::post('/kantin/rekening', [MobileApiController::class, 'kantinStoreRekening']);

    // IoT Device & Biometric Endpoints for Mobile
    Route::get('/esp32/device-status', [\App\Http\Controllers\BiometricController::class, 'getDeviceStatus']);
    Route::post('/biometric/start-enroll', [\App\Http\Controllers\BiometricController::class, 'startEnroll']);
    Route::get('/biometric/enroll-status', [\App\Http\Controllers\BiometricController::class, 'enrollStatus']);

    // Mahasiswa
    Route::get('/mahasiswa/dashboard', [MobileApiController::class, 'mahasiswaDashboard']);
    Route::post('/mahasiswa/pembayaran', [MobileApiController::class, 'mahasiswaStorePembayaran']);
    Route::post('/mahasiswa/biometric', [MobileApiController::class, 'mahasiswaStoreBiometric']);
    Route::get('/mahasiswa/riwayat', [MobileApiController::class, 'mahasiswaRiwayat']);
});
