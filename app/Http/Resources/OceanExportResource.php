<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OceanExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    //    return [
    //        'id_job' => $this->id_job,
    //         'driver' => $this->driver,
    //         'driver_name' => $this->driver_name,
    //         'container_number' => $this->container_number,
    //         'do_number' => $this->do_number,
    //         'customer_name' => $this->customer_name,
    //         'description' => $this->description,
    //     ];
    return [
        'driver' => $this->driver->driver_name,
        'driver_name' => $this->driver->driver_name,
        'container_number' => optional($this->containerDetail)->container_number,
        'id_job' => optional(optional($this->containerDetail)->jobContainer)->id_job,
        'do_number' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->do_number,
        'customer_name' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->customer_name,
        'item_type' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->description,
    ];
    }
}



