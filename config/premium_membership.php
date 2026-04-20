<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Premium Membership Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the premium membership feature.
    |
    */

    // Duration in days for premium membership (default: 365 days = 1 year)
    'duration_days' => env('PREMIUM_MEMBERSHIP_DURATION_DAYS', 365),

    // Default price (can be overridden by GlobalConfig)
    'default_price' => env('PREMIUM_MEMBERSHIP_DEFAULT_PRICE', 100000),
];
