<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class MsJobStatusTracking extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable;
    protected $table = 'tr_shipment_status';
    protected $fillable = [
        'pid',
        'id_tr_shipment_status',
        'id_group_shipment_status',
        'group_name',
        'id_tracking',
        'tracking_name',
        'tracking_order',
        'tracking_level',
        'id_job',
        'timestamp_status',
        'additional',
        'color_status',
        'table_code',
        'created_by',
        'created_datetime',
        'created_ip_address',
        'created_by_browser',
        'modified_by',
        'modified_datetime',
        'modified_ip_address',
        'modified_browser',
        'primary_id',
        'is_active',
        'is_deleted',
    ];
}

