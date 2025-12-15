<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Laravolt\Indonesia\Models\District;

// City codes (from the districts table structure)
// Jakarta Pusat = 3173, Bandung = 3273, Surabaya = 3578

echo "=== Finding District IDs for Top 3 Branches ===\n\n";

// Jakarta Pusat (city_id 151)
$jakarta_districts = District::where('city_code', '3173')->orderBy('id')->limit(3)->get(['id', 'name']);
echo "Jakarta Pusat Districts:\n";
foreach ($jakarta_districts as $d) {
    echo "  - ID: {$d->id}, Name: {$d->name}\n";
}

// Bandung (city_id 23)
$bandung_districts = District::where('city_code', '3273')->orderBy('id')->limit(3)->get(['id', 'name']);
echo "\nBandung Districts:\n";
foreach ($bandung_districts as $d) {
    echo "  - ID: {$d->id}, Name: {$d->name}\n";
}

// Surabaya (city_id 444)
$surabaya_districts = District::where('city_code', '3578')->orderBy('id')->limit(3)->get(['id', 'name']);
echo "\nSurabaya Districts:\n";
foreach ($surabaya_districts as $d) {
    echo "  - ID: {$d->id}, Name: {$d->name}\n";
}

// Get the first district for each (most common - city center)
if ($jakarta_districts->isNotEmpty()) {
    echo "\n=== Recommended Origin IDs ===\n";
    echo "Jakarta Pusat (default): " . $jakarta_districts->first()->id . "\n";
    echo "Bandung (default): " . $bandung_districts->first()->id . "\n";
    echo "Surabaya (default): " . $surabaya_districts->first()->id . "\n";
}
