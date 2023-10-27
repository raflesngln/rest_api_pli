<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MsDriverResource extends JsonResource
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
            'driver_no' => $this->driver_no,
            'driver_name' => $this->driver_name,
            'driver_contact_number1' => $this->driver_contact_number1,
            'driver_contact_number2' => $this->driver_contact_number2,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'ip' => $this->id,
            'created_by' => $this->created_by,
            'vendor_id' => $this->vendor_id,
            'email' => $this->email,
            'password' => $this->password,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'email_verified_at' => $this->email_verified_at,
            'remember_token' => $this->remember_token,
        ];
    }
}
