<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'pengguna_id',
        'tempat_penjemputan',
        'tempat_tujuan',
        'waktu_penjemputan',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'waktu_penjemputan' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_ON_PROCESS = 'on-process';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REJECTED = 'rejected';

    public function penumpang()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id', 'pengguna_id');
    }

    public function assignment()
    {
        return $this->hasOne(OrderAssignment::class, 'order_id', 'order_id');
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_ON_PROCESS, self::STATUS_ASSIGNED]);
    }
    public function scopeByPengguna($query, $penggunaId)
    {
        return $query->where('pengguna_id', $penggunaId);
    }
    public function scopeOutstandingSopir($query)
    {
        return $query->whereIn('status', [ self::STATUS_ON_PROCESS, self::STATUS_ASSIGNED, self::STATUS_CONFIRMED]);
    }
    public function scopeOutstandingKepalaSopir($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING]);
    }

    public function scopeRiwayatPenumpang($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_COMPLETED, self::STATUS_CANCELED, self::STATUS_REJECTED]);
    }

    public function scopeRiwayatSopir($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_COMPLETED]);
    }

    public function scopeRiwayatKepalaSopir($query)
    {
        return $query->whereIn('status', [
            self::STATUS_ASSIGNED,
            self::STATUS_ON_PROCESS,
            self::STATUS_CONFIRMED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELED,
            self::STATUS_REJECTED,
            ]);
    }
}
