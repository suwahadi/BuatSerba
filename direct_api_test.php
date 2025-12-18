<?php

// Get the API key from .env
$apiKey = 'f90201222956e5fe2cb36531ff93c3a0'; // This is from your .env file

echo "Testing Rajaongkir API directly with cURL\n";
echo "========================================\n\n";

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
echo 'Response: '.$response."\n";

// Parse response
$data = json_decode($response, true);
if (isset($data['status']) && $data['status'] == 'success') {
    echo 'SUCCESS! Found '.count($data['data'])." provinces\n";
    if (! empty($data['data'])) {
        echo 'Sample: '.$data['data'][0]['province'].' (ID: '.$data['data'][0]['province_id'].")\n";
    }
} else {
    echo "FAILED!\n";
    if (isset($data['meta'])) {
        echo 'Error: '.$data['meta']['message'].' (Code: '.$data['meta']['code'].")\n";
    }
}
