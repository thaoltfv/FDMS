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
            'endpoint' => env('DIGITAL_ENDPOINT'),
            'use_path_style_endpoint' => env('DIGITAL_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',

        ],

        'spaces' => [
            'driver' => 's3',
            'key' => env('DIGITAL_ACCESS_KEY_ID'),
            'secret' => env('DIGITAL_SECRET_ACCESS_KEY'),
            'endpoint' => env('DIGITAL_ENDPOINT'),
            'url'       => env('DIGITAL_URL'),
            'region' => env('DIGITAL_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('DIGITAL_BUCKET'),
            'use_path_style_endpoint' => env('DIGITAL_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',
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

        'firebase' => [
            'driver'   => 's3',
            'key'      => 'AIzaSyDBtItcceeY59WBMrJsB59RdpwTgikgUqw',
            'secret'   => '-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCr5dVp4Q2+f1n4\n/jvnSzeNciubSDxCZpG3XpFE3Eaw5v1w/aAsn3yiwKODf8eigBd9aFibtgRtfjeS\nVKhvZ7Zz6ipY4x4jAwpwE+vN4zULq0bnAxv8gaAJ4wxa+NOofbqCv2ZiVdrpppxT\nkRM5yrzgQPWZuojvzH5rzYhaa7k6PcftbDBaPhH77hxCj0CDxdB8+356azvnfIRK\ncrP2RA/5Qr1IiTtXn5wqCnjUIS9U8OfpUrzMCMgSBCJq6NKn2DcxhMoBnII0JsGL\nWuoNeHrFYixfiOQApirxX1i18/XGot4AHe5j5H1mEbbczxeeCkJkc2ArS6F6MZ84\n2Ak745mvAgMBAAECggEAHsDuzpOKDvPf1/E8LGRDmxW7AXwJ8+M/3KuZl9VE/G9H\nFoj5uCIMfV2zo1ogEE2UZkZd2/XTkvdK3+4veEb42IIjc2WmxaLMokKeJGaq3dyV\nwczHnARg6oPpePkdfP6JeMzfd2Ze1QCO5VCGPQethP16MzexOh2+3HCYHX06VVXe\nkKob2YQyL6clvFiW9FffGuG71Hqxncy3LiPpBXkYC2XurZcatezp2k0m3wN+rhyf\nzdCv5fdWmG68gX51HGqfZ7ba2VrhNaaileLE9oEvxU7VNMaXNQ6K87b56XZAhmO3\nTM0ph3v5sTaotNiDZI+lr16sJIbNHVYnMWFgdpyoqQKBgQDvXUXu1b7rxe3Vb52O\ni8UFGOfXaUBFSMn82ngKgxcDhOyUp8cihLDndqxIZx9whUJZIgV07TvHcuZxkxq7\nRPllINqJKWzi+pkGhuWTe4zTboDUZEdKH9o7Kc1i5k7NXaz553wvsjAsqvQdeaHY\nVyCt637RkFicZdGHuvvOurPPJQKBgQC32DUtz1dwtfYHNjFmOqpHlhns18cHSkJe\nrVRwQlTcq+z2WvuDU/pN7L8rry0+p/Wxl/pA5B9/6r2aIYQxZJRIxHtMbv9KrFYz\nyj3zC8r8BX4viD2mSY9kQbWZ1op/y9cL+Z4txRTnW3+L/gnOTtC6VOwCRptjIthi\nQqgdbCLnQwKBgBKdtXeqxgt+PE4SPTW95xfLZRSayfXZgGQa9aUTpvGOH/w7xEAK\nA9wsnZ+P7aEJrCxUegoq/TA43nVM1JJl/eOAl559S0un48jfKvG6dSxodrqOBgFc\n0tMxIBkmAlD0jSRufXkUKaz/qra0JAM4W4FLRHS1/j5DqPUedMESQjPFAoGBAJ8h\nd05ytVYrOo0dfH7ncrLKGyCl2XHW9kHrODZ/Q+NKsa7ALAqN6w/+R68xTzF3wrR5\nPzViAF4BuyIptrnmPHAMGkmbnkBtkaP8f8jbwVSKEKJmBC7mZ0a5lc5WJMXflAGa\njC9D8wcbbPio6KX6FLPkg/CoWyHhbHkJB2mIS2HvAoGBALq5dK8CRghKZ0DcmbPE\nDoPit8iVlojTnqw5KXDtmrYWL6oOzpjIxS29XC9Ex4+LK7pSsYPRs5FvNU9tFj2P\npkIe5YNERgTIbtDRLF+7SdujuDbE7vITS2gFuq1GMNUWU7pGrg3ANX/QmMa7A5OT\nG58DYV6zt/sEz8Hl1pBE5qq1\n-----END PRIVATE KEY-----\n',
            'bucket'   => 'fastvalue-trial.appspot.com',
            'endpoint' => 'https://firebasestorage.googleapis.com',
        ],

    ],

];
