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
    //     select dp.driver, dr.driver_name, cdtl.container_number, mcnt.id_job, mjb.do_number,mjb.customer_name,mjb.description AS item_type
    //      from ms_dispatch as dp
    //      LEFT JOIN ms_driver as dr on dp.driver=dr.driver_no
    //      LEFT JOIN ms_container_detail as cdtl on dp.id_container_detail=cdtl.id
    //      LEFT JOIN ms_job_container as mcnt on cdtl.id_job_container=mcnt.id_job_container
    //      LEFT JOIN ms_job as mjb on mcnt.id_job=mjb.id_job

    //     ];
    return [
        'driver' => $this->driver->driver_name,
        'driver_name' => $this->driver->driver_name,
        'container_number' => optional($this->containerDetail)->container_number,
        'id_job' => optional(optional($this->containerDetail)->jobContainer)->id_job,
        'do_number' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->do_number,
        'customer_name' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->customer_name,
        'item_type' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->description,
        // 'consignee_name' => optional(optional(optional($this->containerDetail)->jobContainer)->job)->description,
    ];
    }
}



