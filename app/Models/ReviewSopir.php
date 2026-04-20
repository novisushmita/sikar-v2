<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewSopir extends Model
{
    protected $table = 'review_sopir';
    protected $primaryKey = 'review_id';

    protected $fillable = [
        'tanggal',
        'review',
        'sopir_id',
        'pengguna_id',
        'order_id',
    ];

    public function sopir()
    {
        return $this->belongsTo(Sopir::class, 'sopir_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
