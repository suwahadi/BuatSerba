<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Run migrations
echo "Starting migrations...\n";
$exitCode = $kernel->call('migrate:reset', ['--force' => true]);
echo "Reset complete. Exit code: $exitCode\n";

$exitCode = $kernel->call('migrate');
echo "Migration complete. Exit code: $exitCode\n";

// Check if tables exist
$db = app('db');
$tables = $db->select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");

echo "\nTables created:\n";
foreach ($tables as $table) {
    echo "  - " . $table->TABLE_NAME . "\n";
}

// Check if order_shipping_info exists
$hasOrderShippingInfo = $db->select("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_shipping_info'");
echo "\norder_shipping_info exists: " . (count($hasOrderShippingInfo) > 0 ? "YES" : "NO") . "\n";

// Check skus table columns
$columns = $db->select("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'skus'");
echo "\nSKUs table columns:\n";
foreach ($columns as $column) {
    echo "  - " . $column->COLUMN_NAME . "\n";
}

echo "\nRunning seeders...\n";
$exitCode = $kernel->call('db:seed');
echo "Seeder complete. Exit code: $exitCode\n";

// Check SKU count
$skuCount = $db->table('skus')->count();
echo "\nSKU count: $skuCount\n";

echo "\nTest complete!\n";
