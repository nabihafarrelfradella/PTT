<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantin extends Model
{
    protected $fillable = [
        'user_id', 'nama_kantin', 'no_stand', 'status_verifikasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rekening()
    {
        return $this->hasOne(RekeningKantin::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
