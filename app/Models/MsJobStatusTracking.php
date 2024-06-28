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
        'pid ',
        'id_tr_shipment_status',
        'id_job',
        'tracking_name',
        'moda_transport',
        'primary_id',
        'id_tracking',
        'id_group_shipment_status',
        'color_status',
        'status_name',
        'icon_name',
        'created_by',
        'created_datetime',
        'created_ip_address',
        'created_by_browser',
        'modified_by',
        'modified_datetime',
        'modified_ip_address',
        'modified_browser',
        'is_active',
        'is_deleted',
        'table_code',
        'status_code',
        'is_publish',
        'desc_en',
        'desc_in',
        'bc20',
        'bc23',
        'rh',
        'order',
        'pibk',
        'level',
    ];
}
