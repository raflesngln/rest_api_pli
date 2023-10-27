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
            'id',
            'driver_no',
            'driver_name',
            'driver_contact_number1',
            'driver_contact_number2',
            'is_active',
            'is_deleted',
            'ip',
            'created_by',
            'vendor_id',
            'email',
            'password',
            'created_at',
            'updated_at',
            'email_verified_at',
            'remember_token',
        ];
    }
}
