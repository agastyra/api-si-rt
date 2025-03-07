<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RumahCollection extends ResourceCollection
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
            "data" => $this->collection
        ];
    }
}
