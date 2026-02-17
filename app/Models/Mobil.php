<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    protected $table = 'mobil';
    protected $primaryKey = 'mobil_id';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'deskripsi',
        'availability'
    ];

    public function assignments()
    {
        return $this->hasMany(OrderAssignment::class, 'mobil_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 1);
    }
}