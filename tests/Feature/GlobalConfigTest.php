<?php

use App\Models\GlobalConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('global config table exists', function () {
    expect(\Schema::hasTable('global_configs'))->toBeTrue();
});

test('global config can be created', function () {
    $config = GlobalConfig::create([
        'key' => 'test_key',
        'value' => 'test_value',
        'description' => 'Test description',
        'sort' => 100,
    ]);

    $this->assertDatabaseHas('global_configs', [
        'key' => 'test_key',
        'value' => 'test_value',
    ]);
});

test('global config seeder creates default configs', function () {
    $this->seed(\Database\Seeders\GlobalConfigSeeder::class);

    expect(GlobalConfig::count())->toBeGreaterThanOrEqual(12);

    $siteConfig = GlobalConfig::where('key', 'site_name')->first();
    expect($siteConfig)->not->toBeNull()
        ->and($siteConfig->value)->toBe('Buatserba.com');
});
