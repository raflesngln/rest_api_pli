<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobDispatchResource extends JsonResource
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
            'id_job' => $this->id_job,
            'customer_name' => $this->customer_name,
            'delivery_loc' => $this->delivery_loc,
            'driver' => $this->driver,
            'est_time' => $this->est_time,
            'koli' => $this->koli,
        ];
    }
}
