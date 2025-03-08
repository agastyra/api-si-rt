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
            "data" => $this->collection->transform(function ($transaksi) use ($request) {
                return [
                    "id" => $transaksi->id,
                    "no_transaksi" => $transaksi->no_transaksi,
                    "tanggal_transaksi" => $transaksi->tanggal_transaksi,
                    "rumah" => $transaksi->rumah
                        ? (new RumahCollection(collect([$transaksi->rumah])))->toArray($request)["data"][0] ?? null
                        : null,
                    "transaksi_detail" => $transaksi->transaksi_detail
                        ? new TransaksiDetailCollection($transaksi->transaksi_detail)
                        : null,
                    "created_by" => $transaksi->created_by,
                    "updated_by" => $transaksi->updated_by
                ];
            })
        ];
    }
}
