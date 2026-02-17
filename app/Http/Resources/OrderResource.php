<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'order_id' => $this->order_id,
            'tempat_penjemputan' => $this->tempat_penjemputan,
            'tempat_tujuan' => $this->tempat_tujuan,
            'waktu_penjemputan' => $this->waktu_penjemputan->format('Y-m-d H:i:s'),
            'keterangan' => $this->keterangan,
            'status' => $this->status,
            'penumpang' => [
                'id' => $this->penumpang->pengguna_id,
                'name' => $this->penumpang->name,
            ],
            'dibuat_pada' => $this->created_at->format('Y-m-d H:i:s'),
            'diupdate_pada' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}