<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'student_id',
        'kantin_id',
        'perangkat_id',
        'total_amount',
        'midtrans_order_id',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function kantin()
    {
        return $this->belongsTo(Kantin::class);
    }

    public function perangkat()
    {
        return $this->belongsTo(PerangkatEsp32::class, 'perangkat_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}