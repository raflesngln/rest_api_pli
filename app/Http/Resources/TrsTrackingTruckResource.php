<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrsTrackingTruckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_dispatch' => $this->id_dispatch,
            'id_tracking' => $this->id_tracking,
            'tracking_date' => $this->tracking_date,
            'title' => $this->title,
            'description' => $this->description,
            'attachment' => $this->attachment,
            'id_done' => $this->id_done,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
