<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class StatusTracking extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable;
    protected $table = 'ms_tracking';
    protected $fillable = [
        'pid ',
        'id_tracking',
        'id_group_shipment_status',
        'color_status',
        'status_name',
        'moda_transport',
        'is_delete',
        'is_active',
        'is_publish',
    ];
}

