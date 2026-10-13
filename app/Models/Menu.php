<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $appends = [
        'foto_url',
        'kategori_display',
        'stock_display',
        'effective_category',
    ];

    protected $fillable = [
        'kantin_id',
        'nama_menu',
        'kategori',
        'harga',
        'stock',
        'foto_menu',
    ];

    public function kantin()
    {
        return $this->belongsTo(Kantin::class);
    }

    /**
     * Get image URL with high-quality fallback based on menu name
     */
    public function getFotoUrlAttribute()
    {
        if (!empty($this->foto_menu)) {
            if (str_starts_with($this->foto_menu, 'http')) {
                return $this->foto_menu;
            }
            return asset('storage/' . $this->foto_menu);
        }

        $name = strtolower($this->nama_menu ?? '');
        if (str_contains($name, 'nasi goreng')) {
            return 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif (str_contains($name, 'ayam')) {
            return 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif (str_contains($name, 'teh')) {
            return 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif (str_contains($name, 'mie')) {
            return 'https://images.unsplash.com/photo-1585032226651-759b368d7246?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif (str_contains($name, 'jeruk') || str_contains($name, 'jus')) {
            return 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif (str_contains($name, 'kopi')) {
            return 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif (str_contains($name, 'bakso')) {
            return 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        } elseif ($this->kategori_display === 'Minuman') {
            return 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        }

        return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
    }

    /**
     * Get formatted category title (Makanan / Minuman)
     */
    public function getKategoriDisplayAttribute()
    {
        if (!empty($this->attributes['kategori'] ?? null)) {
            return ucfirst($this->attributes['kategori']);
        }

        $name = strtolower($this->nama_menu ?? '');
        if (str_contains($name, 'teh') || str_contains($name, 'jus') || str_contains($name, 'kopi') || str_contains($name, 'es ') || str_contains($name, 'air') || str_contains($name, 'minum')) {
            return 'Minuman';
        }

        return 'Makanan';
    }

    /**
     * Get formatted stock (e.g., "24 Porsi" or "100 Gelas")
     */
    public function getStockDisplayAttribute()
    {
        $unit = ($this->kategori_display === 'Minuman') ? 'Gelas' : 'Porsi';
        return $this->stock . ' ' . $unit;
    }

    /**
     * Get effective normalized category (makanan / minuman)
     */
    public function getEffectiveCategoryAttribute()
    {
        $cat = strtolower(trim($this->attributes['kategori'] ?? ''));
        if ($cat === 'minuman') {
            return 'minuman';
        }

        $name = strtolower($this->nama_menu ?? '');
        if (str_contains($name, 'teh') || str_contains($name, 'jus') || str_contains($name, 'kopi') || str_contains($name, 'es ') || str_contains($name, 'boba') || str_contains($name, 'soda') || str_contains($name, 'air') || str_contains($name, 'sirup') || str_contains($name, 'syrup') || str_contains($name, 'drink')) {
            return 'minuman';
        }

        return 'makanan';
    }

}
