<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusTrackingJob extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pid' => $this->pid,
            'id_tr_shipment_status' => $this->id_tr_shipment_status,
            'id_group_shipment_status' => $this->id_group_shipment_status,
            'id_job' => $this->id_job,
            'tracking_name' => $this->tracking_name,
            'moda_transport' => $this->moda_transport,
            'primary_id' => $this->primary_id,
        ];
    }
}
