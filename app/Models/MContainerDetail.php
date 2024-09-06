<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MContainerDetail extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'ms_container_detail';
    protected $fillable = [
        'id',
        'id_job_container',
        'container_number',
        'created_datetime',
        'created_by',
        'modified_by',
        'modified_datetime',
        'container_status',
        'id_job',
        'seal_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = ['pid'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];
}
