<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RajaongkirService;

class TestBekasiShipping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bekasi-shipping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test shipping calculation to Bekasi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(RajaongkirService $rajaongkir)
    {
        $this->info('Testing Rajaongkir API Integration');
        $this->info('==================================');

        // Test 1: Get provinces
        $this->info('1. Getting provinces...');
        $provinces = $rajaongkir->getProvinces();
        $this->info('Found ' . count($provinces) . ' provinces');

        // Find West Java province (ID 5 based on our debug output)
        $westJava = null;
        foreach ($provinces as $province) {
            if (stripos($province['name'] ?? '', 'jawa barat') !== false) {
                $westJava = $province;
                break;
            }
        }

        if (!$westJava) {
            $this->error('Could not find West Java province');
            return 1;
        }

        $this->info('West Java Province: ' . ($westJava['name'] ?? '') . ' (ID: ' . ($westJava['id'] ?? '') . ')');

        // Test 2: Get cities in West Java
        $this->info('2. Getting cities in West Java...');
        $cities = $rajaongkir->getCities($westJava['id']);
        $this->info('Found ' . count($cities) . ' cities');

        // Find Bekasi city
        $becakasi = null;
        foreach ($cities as $city) {
            // Check both possible field names
            $cityName = $city['city_name'] ?? $city['name'] ?? '';
            if (stripos($cityName, 'bekasi') !== false) {
                $becakasi = $city;
                break;
            }
        }

        if (!$becakasi) {
            $this->error('Could not find Bekasi city');
            return 1;
        }

        // Use the correct field names
        $cityId = $becakasi['city_id'] ?? $becakasi['id'] ?? '';
        $cityName = $becakasi['city_name'] ?? $becakasi['name'] ?? '';
        $this->info("Bekasi City: {$cityName} (ID: {$cityId})");

        // Test 3: Get districts in Bekasi
        $this->info('3. Getting districts in Bekasi...');
        $districts = $rajaongkir->getDistricts($cityId);
        $this->info('Found ' . count($districts) . ' districts');

        // Take the first district as an example
        $district = $districts[0] ?? null;
        if (!$district) {
            $this->error('Could not find any districts in Bekasi');
            return 1;
        }

        // Use the correct field names for district
        $districtId = $district['subdistrict_id'] ?? $district['id'] ?? '';
        $districtName = $district['subdistrict_name'] ?? $district['name'] ?? '';
        $this->info("Sample District: {$districtName} (ID: {$districtId})");

        // Test 4: Calculate shipping cost
        $this->info('4. Calculating shipping cost...');
        $params = [
            'origin' => '1391', // Example origin (should be replaced with actual warehouse location)
            'destination' => $districtId,
            'weight' => '1000',
            'courier' => 'jne:sicepat:ide:sap:jnt:ninja:tiki:lion:anteraja:pos:ncs:rex:rpx:sentral:star:wahana:dse',
            'price' => 'lowest'
        ];

        $result = $rajaongkir->calculateShippingCost($params);

        if (!empty($result)) {
            $this->info('Shipping calculation successful!');
            $this->info('Results:');
            $this->line(print_r($result, true));
        } else {
            $this->error('Failed to calculate shipping cost');
        }

        $this->info('Test completed.');
        return 0;
    }
}