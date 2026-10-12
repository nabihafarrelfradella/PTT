<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RekeningKantin extends Model
{
    protected $fillable = ['kantin_id', 'bank_name', 'account_number', 'account_holder_name'];

    public function kantin() { return $this->belongsTo(Kantin::class); }
}
