<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MsFilesResource extends JsonResource
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
            'modul'=>$this->modul,
            'pi_table'=>$this->pi_table,
            'id_file'=>$this->id_file,
            'file_name'=>$this->file_name,
            'subject'=>$this->subject,
            'description'=>$this->description,
            'extension'=>$this->extension,
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
            'expired_date'=>$this->expired_date,
            'dept'=>$this->dept,
        ];
    }
}
