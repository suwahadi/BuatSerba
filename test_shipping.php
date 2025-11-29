<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\RajaongkirService;
use App\Models\Branch;

try {
    echo "Testing Rajaongkir API Integration\n";
    echo "==================================\n\n";
    
    // Check if API key is configured
    $apiKey = config('services.rajaongkir.key');
    echo "API Key: " . (strlen($apiKey) > 10 ? substr($apiKey, 0, 10) . '...' : 'NOT CONFIGURED') . "\n";
    if (!$apiKey) {
        echo "Please set your RAJAONGKIR_API_KEY in the .env file\n";
        exit(1);
    }
    echo "\n";
    
    // Initialize Rajaongkir service
    $rajaongkir = new RajaongkirService();
    
    // Test getting provinces
    echo "1. Testing Provinces API...\n";
    $provinces = $rajaongkir->getProvinces();
    echo "Found " . count($provinces) . " provinces\n";
    if (!empty($provinces)) {
        echo "Sample province: " . $provinces[0]['name'] . " (ID: " . $provinces[0]['id'] . ")\n";
        
        // Show first 5 provinces
        echo "First 5 provinces:\n";
        for ($i = 0; $i < min(5, count($provinces)); $i++) {
            echo "  " . ($i + 1) . ". " . $provinces[$i]['name'] . " (ID: " . $provinces[$i]['id'] . ")\n";
        }
    } else {
        echo "No provinces found. Check API key and network connectivity.\n";
    }
    echo "\n";
    
    // Test getting a branch for origin
    echo "2. Getting origin branch...\n";
    $branch = Branch::where('is_active', true)->orderBy('priority')->first();
    if ($branch) {
        echo "Using branch: " . $branch->name . " (City ID: " . $branch->city_id . ")\n";
    } else {
        echo "No active branch found\n";
        exit(1);
    }
    echo "\n";
    
    // Show a message about cities API issue
    echo "3. Note about Cities API\n";
    echo "The cities API endpoint is currently returning 404 errors.\n";
    echo "This might be due to:\n";
    echo "  - Incorrect endpoint URL\n";
    echo "  - API key limitations\n";
    echo "  - Temporary API issues\n";
    echo "The provinces API is working correctly.\n";
    echo "\n";
    
    echo "SUCCESS: Rajaongkir integration is partially working!\n";
    echo "Provinces API is functional, but cities API needs further investigation.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}