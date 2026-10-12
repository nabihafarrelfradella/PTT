<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\MahasiswaController;

// Halaman Utama: Layar Kios Pemesanan Mandiri POS (Tablet Kasir Kantin)
Route::get('/', function () {
    $menus = \App\Models\Menu::all();
    $kantin = \App\Models\Kantin::first();
    $testStudents = \App\Models\User::where('role', 'mahasiswa')
        ->whereHas('biometric')
        ->with(['biometric', 'debitAccount'])
        ->take(2)
        ->get();
    return view('welcome', compact('menus', 'kantin', 'testStudents'));
})->name('kios');

Route::get('/kios', function () {
    return redirect('/');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register Routes
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'process']);

// ============ ADMIN ============
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        if (auth()->user()->role !== 'admin') return redirect('/login');
        return redirect()->route('admin.kantin');
    });
    Route::get('/kantin', [AdminController::class, 'kantinApproval'])->name('admin.kantin');
    Route::post('/kantin/{id}/approve', [AdminController::class, 'approveKantin'])->name('admin.kantin.approve');
    Route::get('/mahasiswa', [AdminController::class, 'mahasiswaData'])->name('admin.mahasiswa');
    Route::post('/mahasiswa/{id}/reset-bio', [AdminController::class, 'resetBiometric'])->name('admin.mahasiswa.resetBio');
});

// ============ KANTIN ============
Route::middleware('auth')->prefix('kantin')->group(function () {
    Route::get('/', [KantinController::class, 'dashboard'])->name('kantin.dashboard');
    Route::get('/menu', [KantinController::class, 'menu'])->name('kantin.menu');
    Route::post('/menu', [KantinController::class, 'storeMenu'])->name('kantin.menu.store');
    Route::put('/menu/{id}', [KantinController::class, 'updateMenu'])->name('kantin.menu.update');
    Route::delete('/menu/{id}', [KantinController::class, 'deleteMenu'])->name('kantin.menu.delete');
    Route::get('/rekening', [KantinController::class, 'rekening'])->name('kantin.rekening');
    Route::post('/rekening', [KantinController::class, 'storeRekening'])->name('kantin.rekening.store');
});

// ============ MAHASISWA ============
Route::middleware('auth')->prefix('mahasiswa')->group(function () {
    Route::get('/', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/pembayaran', [MahasiswaController::class, 'pembayaran'])->name('mahasiswa.pembayaran');
    Route::post('/pembayaran', [MahasiswaController::class, 'storePembayaran'])->name('mahasiswa.pembayaran.store');
    Route::post('/biometric', [MahasiswaController::class, 'storeBiometric'])->name('mahasiswa.biometric.store');
    Route::get('/riwayat', [MahasiswaController::class, 'riwayat'])->name('mahasiswa.riwayat');
});