<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'photo', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute()
    {
        if (!empty($this->photo)) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            return asset('storage/' . ltrim($this->photo, '/'));
        }
        return null;
    }

    public function kantin()
    {
        return $this->hasOne(Kantin::class);
    }

    public function debitAccount()
    {
        return $this->hasOne(DebitAccount::class);
    }

    public function biometric()
    {
        return $this->hasOne(Biometric::class);
    }
}
