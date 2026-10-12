<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Kantin;
use App\Models\RekeningKantin;
use App\Models\Transaction;

class KantinController extends Controller
{
    // Dashboard Kantin
    public function dashboard()
    {
        $kantin = Kantin::where('user_id', auth()->id())->first();
        $transactions = Transaction::where('kantin_id', $kantin->id ?? 0)
            ->with('student')
            ->latest()
            ->take(10)
            ->get();
        return view('kantin.dashboard', compact('kantin', 'transactions'));
    }

    // Manajemen Menu
    public function menu()
    {
        $kantin = Kantin::where('user_id', auth()->id())->first();
        $menus = Menu::where('kantin_id', $kantin->id ?? 0)->get();
        return view('kantin.menu', compact('kantin', 'menus'));
    }

    // Simpan menu baru
    public function storeMenu(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'kategori' => 'nullable|in:makanan,minuman',
            'foto_menu' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $kantin = Kantin::where('user_id', auth()->id())->first();

        $data = [
            'kantin_id' => $kantin->id,
            'nama_menu' => $request->nama_menu,
            'kategori' => $request->kategori ?? 'makanan',
            'harga' => $request->harga,
            'stock' => $request->stock,
        ];

        if ($request->hasFile('foto_menu')) {
            $data['foto_menu'] = $request->file('foto_menu')->store('menu_photos', 'public');
        }

        Menu::create($data);

        return back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // Update menu
    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'kategori' => 'nullable|in:makanan,minuman',
            'foto_menu' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $menu = Menu::findOrFail($id);
        $data = $request->only('nama_menu', 'harga', 'stock', 'kategori');

        if ($request->hasFile('foto_menu')) {
            $data['foto_menu'] = $request->file('foto_menu')->store('menu_photos', 'public');
        }

        $menu->update($data);
        return back()->with('success', 'Menu berhasil diperbarui!');
    }

    // Hapus menu
    public function deleteMenu($id)
    {
        Menu::findOrFail($id)->delete();
        return back()->with('success', 'Menu berhasil dihapus!');
    }

    // Rekening Pencairan
    public function rekening()
    {
        $kantin = Kantin::where('user_id', auth()->id())->first();
        $rekening = RekeningKantin::where('kantin_id', $kantin->id ?? 0)->first();
        return view('kantin.rekening', compact('kantin', 'rekening'));
    }

    // Simpan/update rekening
    public function storeRekening(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder_name' => 'required|string',
        ]);

        $kantin = Kantin::where('user_id', auth()->id())->first();

        RekeningKantin::updateOrCreate(
            ['kantin_id' => $kantin->id],
            [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
            ]
        );

        return back()->with('success', 'Data rekening berhasil disimpan!');
    }
}
