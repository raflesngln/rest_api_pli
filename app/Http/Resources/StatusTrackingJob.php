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
            'pid'=>$this->pid,
            'id_tr_shipment_status'=>$this->id_tr_shipment_status,
            'id_group_shipment_status'=>$this->id_group_shipment_status,
            'id_job'=>$this->id_job,
            'tracking_name'=>$this->tracking_name,
            'moda_transport'=>$this->moda_transport,
            'primary_id'=>$this->primary_id,
            'id_tracking'=>$this->id_tracking,
            'color_status'=>$this->color_status,
            'status_name'=>$this->status_name,
            'icon_name'=>$this->icon_name,
            'created_by'=>$this->created_by,
            'created_datetime'=>$this->created_datetime,
            'created_ip_address'=>$this->created_ip_address,
            'created_by_browser'=>$this->created_by_browser,
            'modified_by'=>$this->modified_by,
            'modified_datetime'=>$this->modified_datetime,
            'modified_ip_address'=>$this->modified_ip_address,
            'modified_browser'=>$this->modified_browser,
            'is_active'=>$this->is_active,
            'is_deleted'=>$this->is_deleted,
            'table_code'=>$this->table_code,
            'status_code'=>$this->status_code,
            'is_publish'=>$this->is_publish,
            'desc_en'=>$this->desc_en,
            'desc_in'=>$this->desc_in,
            'bc20'=>$this->bc20,
            'bc23'=>$this->bc23,
            'rh'=>$this->rh,
            'order'=>$this->order,
            'pibk'=>$this->pibk,
            'level'=>$this->level,
        ];
    }
}
