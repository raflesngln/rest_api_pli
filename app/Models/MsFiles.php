<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MsFiles extends Model
{
    use HasFactory,HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'ms_files';
    protected $primaryKey = 'pid'; // Your custom primary key
    public $incrementing = false; // Set to false if your primary key is not an auto-incrementing integer
    protected $keyType = 'string';
    protected $fillable = [
        'pid',
        'modul',
        'pi_table',
        'id_file',
        'file_name',
        'subject',
        'description',
        'extension',
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
        'expired_date',
        'dept',
        'latitude',
        'longitude',
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
