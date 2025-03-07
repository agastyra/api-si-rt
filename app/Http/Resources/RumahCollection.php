<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RumahCollection extends ResourceCollection
{

    public function __construct(
        $resource,
        public mixed $message = null
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
            "data" => $this->collection->transform(function ($rumah) {
                return [
                    "id" => $rumah->id,
                    "blok" => $rumah->blok,
                    "status_rumah" => $rumah->status_rumah,
                    "penghuni_rumah" => PenghuniRumahResource::collection($rumah->penghuni_rumah),
                    "created_by" => $rumah->created_by,
                    "updated_by" => $rumah->updated_by
                ];
            })
        ];
    }
}
