<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusTrackingJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'pid'=>$this->pid,
        'id_tr_shipment_status'=>$this->id_tr_shipment_status,
        'id_group_shipment_status'=>$this->id_group_shipment_status,
        'group_name'=>$this->group_name,
        'id_tracking'=>$this->id_tracking,
        'tracking_name'=>$this->tracking_name,
        'tracking_order'=>$this->tracking_order,
        'tracking_level'=>$this->tracking_level,
        'id_job'=>$this->id_job,
        'timestamp_status'=>$this->timestamp_status,
        'additional'=>$this->additional,
        'color_status'=>$this->color_status,
        'table_code'=>$this->table_code,
        'created_by'=>$this->created_by,
        'created_datetime'=>$this->created_datetime,
        'created_ip_address'=>$this->created_ip_address,
        'created_by_browser'=>$this->created_by_browser,
        'modified_by'=>$this->modified_by,
        'modified_datetime'=>$this->modified_datetime,
        'modified_ip_address'=>$this->modified_ip_address,
        'modified_browser'=>$this->modified_browser,
        'primary_id'=>$this->primary_id,
        'is_active'=>$this->is_active,
        'is_deleted'=>$this->is_deleted,
        ];
    }
}

