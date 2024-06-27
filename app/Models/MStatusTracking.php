<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class MStatusTracking extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable;
    protected $table = 'ms_tracking';
    protected $fillable = [
        'pid ',
        'id_tr_shipment_status',
        'id_group_shipment_status',
        'id_job',
        'tracking_name',
        'moda_transport',
        'primary_id',
    ];
}

