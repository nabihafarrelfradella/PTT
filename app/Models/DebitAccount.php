<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DebitAccount extends Model
{
    protected $fillable = ['user_id', 'bank_name', 'card_token_masked', 'card_token', 'is_active'];

    public function user() { return $this->belongsTo(User::class); }
}
