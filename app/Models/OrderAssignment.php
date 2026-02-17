<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAssignment extends Model
{
    protected $table = 'order_assignment';
    protected $primaryKey = 'assign_id';

    protected $fillable = [
        'order_id',
        'mobil_id',
        'sopir_id',
    ];
    protected $casts = [
        'mobil_id' => 'string',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function sopir()
    {
        return $this->belongsTo(Pengguna::class, 'sopir_id');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
}
