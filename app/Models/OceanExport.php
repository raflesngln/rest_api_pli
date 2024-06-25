<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\MsDriver;



class OceanExport extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable;
    protected $table = 'ms_dispatch';
    protected $fillable = [
        'driver',
        'driver_name',
        'container_number',
        'id_job',
        'do_number',
        'customer_name',
        'description',
    ];
    public function driver()
    {
        return $this->belongsTo(MsDriver::class, 'driver', 'driver_no');
    }
    public function containerDetail()
    {
        return $this->belongsTo(MsContainerDetail::class, 'id_container_detail', 'id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];
}
