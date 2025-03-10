<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransaksiDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->transform(function ($transaksi_detail) {
            return [
                "id" => $transaksi_detail->id,
                "tipe_transaksi" => $transaksi_detail->tipe_transaksi,
                "periode_bulan" => $transaksi_detail->periode_bulan,
                "periode_tahun" => $transaksi_detail->periode_tahun,
                "nominal" => $transaksi_detail->nominal,
                "created_by" => $transaksi_detail->created_by,
                "updated_by" => $transaksi_detail->updated_by
            ];
        })->toArray();
    }
}
