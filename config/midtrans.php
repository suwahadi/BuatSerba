<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Midtrans Core API integration.
    | Using Core API instead of Snap for direct payment method control.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Core API specific settings
    'core_api' => [
        'notification_url' => env('MIDTRANS_NOTIFICATION_URL', '/midtrans/notification'),
        'currency' => env('MIDTRANS_CURRENCY', 'IDR'),
    ],

    // Redirect URLs after transaction
    'redirect_urls' => [
        'finish' => env('MIDTRANS_FINISH_URL', '/midtrans/finish'),
        'unfinish' => env('MIDTRANS_UNFINISH_URL', '/midtrans/unfinish'),
        'error' => env('MIDTRANS_ERROR_URL', '/midtrans/error'),
    ],

    'transaction_timeout' => env('MIDTRANS_TRANSACTION_TIMEOUT', 30), // in minutes
];
