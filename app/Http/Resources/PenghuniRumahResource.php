<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenghuniRumahResource extends JsonResource
{
    public function __construct(
        $resource,
    )
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "penghuni" => collect($this->penghuni),
            "periode_bulan_mulai" => $this->periode_bulan_mulai,
            "periode_bulan_selesai" => $this->periode_bulan_selesai,
            "periode_tahun_mulai" => $this->periode_tahun_mulai,
            "periode_tahun_selesai" => $this->periode_tahun_selesai,
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,
        ];
    }
}
