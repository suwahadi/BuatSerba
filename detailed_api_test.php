<?php

// Get the API key from .env
$apiKey = 'f90201222956e5fe2cb36531ff93c3a0'; // This is from your .env file

echo "Testing Rajaongkir API directly with cURL - Detailed Test\n";
echo "========================================================\n\n";

// Test provinces endpoint
echo "1. Testing Provinces API...\n";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://rajaongkir.komerce.id/api/v1/destination/province');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'key: '.$apiKey,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo 'HTTP Code: '.$httpCode."\n";
$data = json_decode($response, true);

if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
    echo 'SUCCESS! Found '.count($data['data'])." provinces\n";
    // Find West Java
    $westJava = null;
    foreach ($data['data'] as $province) {
        if (strpos(strtolower($province['name']), 'jawa barat') !== false) {
            $westJava = $province;
            break;
        }
    }

    if ($westJava) {
        echo 'Found West Java: '.$westJava['name'].' (ID: '.$westJava['id'].")\n";

        // Test cities endpoint for West Java
        echo "\n2. Testing Cities API for West Java...\n";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://rajaongkir.komerce.id/api/v1/destination/city?province_id='.$westJava['id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'key: '.$apiKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo 'HTTP Code: '.$httpCode."\n";
        $cityData = json_decode($response, true);

        if (isset($cityData['meta']['status']) && $cityData['meta']['status'] == 'success') {
            echo 'SUCCESS! Found '.count($cityData['data'])." cities\n";
            if (! empty($cityData['data'])) {
                // Find Bekasi
                $bekasi = null;
                foreach ($cityData['data'] as $city) {
                    if (strpos(strtolower($city['city_name']), 'bekasi') !== false) {
                        $bekasi = $city;
                        break;
                    }
                }

                if ($bekasi) {
                    echo 'Found Bekasi: '.$bekasi['city_name'].' (ID: '.$bekasi['city_id'].")\n";
                } else {
                    echo 'Bekasi not found. First city: '.$cityData['data'][0]['city_name'].' (ID: '.$cityData['data'][0]['city_id'].")\n";
                }
            }
        } else {
            echo "FAILED to get cities!\n";
            echo 'Response: '.$response."\n";
        }
    } else {
        echo "West Java not found in provinces list\n";
    }
} else {
    echo "FAILED to get provinces!\n";
    echo 'Response: '.$response."\n";
}
