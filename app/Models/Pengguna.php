<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    protected $primaryKey = 'pengguna_id';
    protected $fillable = [
        'name',
        'token',
        'role',
        'nomor'
    ];
    
    protected $hidden = [
        'token'
    ];

    const ROLE_PENUMPANG = 'penumpang';
    const ROLE_SOPIR = 'sopir';
    const ROLE_KEPALA_SOPIR = 'kepala_sopir';

    public static function roles(): array
    {
        return [
            self::ROLE_PENUMPANG,
            self::ROLE_SOPIR,
            self::ROLE_KEPALA_SOPIR,
        ];
    }

    public function isPenumpang(): bool
    {
        return $this->role === self::ROLE_PENUMPANG;
    }

    public function isSopir(): bool
    {
        return $this->role === self::ROLE_SOPIR;
    }

    public function isKepalaSopir(): bool
    {
        return $this->role === self::ROLE_KEPALA_SOPIR;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'pengguna_id', 'pengguna_id');
    }
}
