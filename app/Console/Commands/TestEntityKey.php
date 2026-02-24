<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEntityKey extends Command
{
    protected $signature = 'test:entity-key';
    protected $description = 'Test entity key generation';

    public function handle(): int
    {
        $seeder = new \Database\Seeders\FilamentPermissionSeeder();
        
        echo "BannerResource: " . $seeder->makeEntityKey('App\Filament\Resources\Banners\BannerResource') . "\n";
        echo "OrderResource: " . $seeder->makeEntityKey('App\Filament\Resources\Orders\OrderResource') . "\n";
        echo "Dashboard: " . $seeder->makeEntityKey('App\Filament\Pages\Dashboard') . "\n";
        echo "LatestOrders: " . $seeder->makeEntityKey('App\Filament\Widgets\LatestOrders') . "\n";
        
        return 0;
    }
}
