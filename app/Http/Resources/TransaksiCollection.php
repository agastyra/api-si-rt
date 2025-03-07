<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransaksiCollection extends ResourceCollection
{
    public function __construct(
        public string $message,
                      $resource
    )
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "message" => $this->message,
            "data" => $this->collection->transform(function ($transaksi) {
                return [
                    "id" => $transaksi->id,
                    "no_transaksi" => $transaksi->no_transaksi,
                    "tanggal_transaksi" => $transaksi->tanggal_transaksi,
                    "rumah" => collect($transaksi->rumah),
                    "transaksi_detail" => new TransaksiDetailCollection($transaksi->transaksi_detail),
                    "created_by" => $transaksi->created_by,
                    "updated_by" => $transaksi->updated_by
                ];
            })
        ];
    }
}
