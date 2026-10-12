<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kantin;
use App\Models\RekeningKantin;
use App\Models\Menu;
use App\Models\DebitAccount;
use App\Models\Biometric;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\PerangkatEsp32;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin PSDKU
        $admin = User::firstOrCreate(
            ['email' => 'admin@polinema.ac.id'],
            [
                'name' => 'Admin PSDKU',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Merchant Kantin
        $userKantin = User::firstOrCreate(
            ['email' => 'sri@kantin.com'],
            [
                'name' => 'Sri Wahyuni',
                'password' => Hash::make('password'),
                'role' => 'kantin',
            ]
        );

        $kantin = Kantin::firstOrCreate(
            ['user_id' => $userKantin->id],
            [
                'nama_kantin' => 'Stand Nasi Goreng Bu Sri',
                'no_stand' => 'Stand 01',
                'status_verifikasi' => 'approved',
            ]
        );

        RekeningKantin::firstOrCreate(
            ['kantin_id' => $kantin->id],
            [
                'bank_name' => 'Bank BCA',
                'account_number' => '8239100234',
                'account_holder_name' => 'Sri Wahyuni'
            ]
        );

        // Perangkat ESP32 Hardware (sesuai ERD)
        $perangkat = PerangkatEsp32::firstOrCreate(
            ['device_id' => 'ESP32-KANTIN-01'],
            [
                'admin_id' => $admin->id,
                'merchant_id' => $kantin->id,
                'nama_perangkat' => 'Kios POS & Scanner AS608 Stand 01',
                'lokasi' => 'Kantin Gedung Utama Meja 1',
                'status' => 'online',
                'last_ping' => now(),
            ]
        );

        // Menus
        $menus = [
            ['nama_menu' => 'Nasi Goreng Spesial', 'harga' => 15000, 'stock' => 50],
            ['nama_menu' => 'Ayam Geprek Sambal Korek', 'harga' => 18000, 'stock' => 40],
            ['nama_menu' => 'Mie Kuah Susu', 'harga' => 12000, 'stock' => 30],
            ['nama_menu' => 'Es Teh Manis', 'harga' => 5000, 'stock' => 100],
            ['nama_menu' => 'Jus Jeruk Segar', 'harga' => 8000, 'stock' => 60],
            ['nama_menu' => 'Kopi Susu Gula Aren', 'harga' => 10000, 'stock' => 45],
        ];

        foreach ($menus as $m) {
            Menu::firstOrCreate(
                ['kantin_id' => $kantin->id, 'nama_menu' => $m['nama_menu']],
                ['harga' => $m['harga'], 'stock' => $m['stock']]
            );
        }

        // 3. Mahasiswa 1: Budi Santoso (Fingerprint ID #1)
        $mhs1 = User::firstOrCreate(
            ['email' => 'budi@student.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]
        );

        DebitAccount::updateOrCreate(
            ['user_id' => $mhs1->id],
            [
                'bank_name' => 'Bank BCA',
                'card_token_masked' => '4811 11** **** 1234',
                'card_token' => 'tok_bca_budi_1234',
                'is_active' => true,
            ]
        );

        Biometric::updateOrCreate(
            ['user_id' => $mhs1->id],
            [
                'fingerprint_id' => 1,
                'fingerprint_hash' => 'FP-1-BUDI-AS608',
                'registered_at' => now(),
            ]
        );

        // 4. Mahasiswa 2: Siti Rahma (Fingerprint ID #2)
        $mhs2 = User::firstOrCreate(
            ['email' => 'siti@student.com'],
            [
                'name' => 'Siti Rahma',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]
        );

        DebitAccount::updateOrCreate(
            ['user_id' => $mhs2->id],
            [
                'bank_name' => 'Bank BRI',
                'card_token_masked' => '5211 22** **** 5678',
                'card_token' => 'tok_bri_siti_5678',
                'is_active' => true,
            ]
        );

        Biometric::updateOrCreate(
            ['user_id' => $mhs2->id],
            [
                'fingerprint_id' => 2,
                'fingerprint_hash' => 'FP-2-SITI-AS608',
                'registered_at' => now(),
            ]
        );

        // Dummy initial transaction history for Budi
        if (Transaction::where('student_id', $mhs1->id)->count() === 0) {
            $tx1 = Transaction::create([
                'transaction_code' => 'TRX-99281-A',
                'student_id' => $mhs1->id,
                'kantin_id' => $kantin->id,
                'perangkat_id' => $perangkat->id,
                'total_amount' => 20000,
                'midtrans_order_id' => 'MID-99281-A',
                'status' => 'success',
            ]);
            $menuNasi = Menu::where('nama_menu', 'Nasi Goreng Spesial')->first();
            $menuTeh = Menu::where('nama_menu', 'Es Teh Manis')->first();
            if ($menuNasi) {
                TransactionDetail::create([
                    'transaction_id' => $tx1->id,
                    'menu_id' => $menuNasi->id,
                    'qty' => 1,
                    'price' => 15000,
                    'subtotal' => 15000,
                ]);
            }
            if ($menuTeh) {
                TransactionDetail::create([
                    'transaction_id' => $tx1->id,
                    'menu_id' => $menuTeh->id,
                    'qty' => 1,
                    'price' => 5000,
                    'subtotal' => 5000,
                ]);
            }
        }
    }
}