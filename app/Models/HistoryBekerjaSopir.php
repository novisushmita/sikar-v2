<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryBekerjaSopir extends Model
{
    protected $table = 'history_bekerja_sopir';
    protected $primaryKey = 'bekerja_id';

    protected $fillable = [
        'tanggal',
        'order_completed',
        'sopir_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'order_completed' => 'integer',
        'sopir_id' => 'integer'
    ];

    /**
     * Relasi ke model Sopir
     */
    public function sopir()
    {
        return $this->belongsTo(Sopir::class, 'sopir_id', 'sopir_id');
    }
}
