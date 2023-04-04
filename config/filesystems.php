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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root'   => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver'   => 's3',
            'key'      => env('DIGITAL_ACCESS_KEY_ID'),
            'secret'   => env('DIGITAL_SECRET_ACCESS_KEY'),
            'region'   => env('DIGITAL_DEFAULT_REGION', 'us-east-1'),
            'bucket'   => env('DIGITAL_BUCKET'),
            'url'       => env('DIGITAL_URL'),
            'visibility' => 'public',

        ],

        'spaces' => [
            'driver' => 's3',
            'key' => env('DIGITAL_ACCESS_KEY_ID'),
            'secret' => env('DIGITAL_SECRET_ACCESS_KEY'),
            'endpoint' => env('DIGITAL_ENDPOINT'),
            'region' => env('DIGITAL_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('DIGITAL_BUCKET'),
            'use_path_style_endpoint' => env('DIGITAL_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'viettel' => [
            'driver' => 's3',
            'key' => env('VIETTEL_ACCESS_KEY_ID'),
            'secret' => env('VIETTEL_SECRET_ACCESS_KEY'),
            'endpoint' => env('VIETTEL_ENDPOINT'),
            'region' => env('VIETTEL_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('VIETTEL_BUCKET'),
            'use_path_style_endpoint' => env('VIETTEL_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'gcs' => [
            'driver'   => 's3',
            'key'      => env('GCS_KEY'),
            'secret'   => env('GCS_SECRET'),
            'region'   => env('GCS_REGION'),
            'bucket'   => env('GCS_BUCKET'),
            'endpoint' => 'https://storage.googleapis.com',
        ],

    ],

];
