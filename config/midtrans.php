<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Midtrans payment gateway integration.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', '/midtrans/notification'),
    'finish_url' => env('MIDTRANS_FINISH_URL', '/midtrans/finish'),
    'unfinish_url' => env('MIDTRANS_UNFINISH_URL', '/midtrans/unfinish'),
    'error_url' => env('MIDTRANS_ERROR_URL', '/midtrans/error'),
    'currency' => env('MIDTRANS_CURRENCY', 'IDR'),
    'transaction_timeout' => env('MIDTRANS_TRANSACTION_TIMEOUT', 30), // in minutes
];