<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerangkatEsp32 extends Model
{
    protected $table = 'perangkat_esp32';

    protected $fillable = [
        'admin_id',
        'merchant_id',
        'device_id',
        'nama_perangkat',
        'lokasi',
        'status',
        'last_ping',
    ];

    protected $casts = [
        'last_ping' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Kantin::class, 'merchant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'perangkat_id');
    }
}