<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    // 'default' => env('obs','FILESYSTEM_DISK', 'local'),
    'default' =>'s3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('HUAWEI_OBS_KEY'),
            'secret' => env('HUAWEI_OBS_SECRET'),
            'region' => env('HUAWEI_OBS_REGION'),
            'bucket' => env('HUAWEI_OBS_BUCKET'),
            'url' => env('HUAWEI_OBS_DOMAIN'),
            'endpoint' => env('HUAWEI_OBS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'options' => [
            'ServerSideEncryption' => 'AES256',
            // 'ServerSideEncryption' => 'aws:kms',
            ],
        ],
        'huaweiobs' => [
            'driver' => 'obs',
            'access_id' => env('HUAWEI_OBS_KEY'),
            'access_key' => env('HUAWEI_OBS_SECRET'),
            'bucket' => env('HUAWEI_OBS_BUCKET'),
            'endpoint' => env('HUAWEI_OBS_ENDPOINT'), // OBS 外网节点或自定义外部域名
            'endpoint_internal' =>'', // 如果为空，则默认使用 endpoint 配置
            'cdnDomain' => env('HUAWEI_OBS_DOMAIN'), // 如果不为空，getUrl会判断cdnDomain是否设定来决定返回的url，如果cdnDomain未设置，则使用endpoint来生成url，否则使用cdn
            'ssl' => env('HUAWEI_OBS_SSL', true), // true to use 'https://' and false to use 'http://'. default is false,
            'prefix' => env('HUAWEI_OBS_PREFIX'), // 路径前缀
            'options' => [],
            'throw' => true,
        ],
        'obs' => [
            'driver' => 's3',
            'key' => env('HUAWEI_OBS_KEY'),
            'secret' => env('HUAWEI_OBS_SECRET'),
            'bucket' => env('HUAWEI_OBS_BUCKET'),
            'region' => env('HUAWEI_OBS_REGION'),
            'endpoint' => env('HUAWEI_OBS_ENDPOINT'),
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
