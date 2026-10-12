<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Biometric extends Model
{
    protected $fillable = ['user_id', 'fingerprint_hash', 'fingerprint_id', 'registered_at'];

    public function user() { return $this->belongsTo(User::class); }
}
