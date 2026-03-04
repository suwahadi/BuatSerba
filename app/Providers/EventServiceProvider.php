<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Listeners\GrantPremiumCashback;
use App\Listeners\UpgradeUserGrade;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderPaid::class => [
            UpgradeUserGrade::class,
            GrantPremiumCashback::class,
        ],
    ];
}
