<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MsTrackingTruckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id',
            'sorting',
            'title',
            'description',
            'is_active',
            'is_deleted',
            'created_at',
            'updated_at',
        ];
    }
}
