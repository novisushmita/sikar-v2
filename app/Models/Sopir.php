<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sopir extends Model
{
    protected $table = 'sopir';
    protected $primaryKey = 'sopir_id';

    protected $fillable = [
        'name',
        'order_completed',
        'order_ongoing',
        'masuk_kerja'
    ];

    protected $casts = [
        'order_completed' => 'integer',
        'order_ongoing' => 'integer',
        'masuk_kerja' => 'integer',
    ];

    public function historyBekerja()
    {
        return $this->hasMany(HistoryBekerjaSopir::class, 'sopir_id', 'sopir_id');
    }

    public function scopeAvailable($query)
    {
        $query->where('order_ongoing', 0);
        return $query->where('masuk_kerja', 1);
        }
        
    public function scopeMasukKerja($query)
    {
        return $query->where('masuk_kerja', 1);
    }

    public function scopeOngoing($query)
    {  
        return $query->where('order_ongoing', 1);
    }
}
