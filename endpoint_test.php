<?php

// Get the API key from .env
$apiKey = 'f90201222956e5fe2cb36531ff93c3a0'; // This is from your .env file

echo "Testing Different Rajaongkir Endpoints\n";
echo "=====================================\n\n";

// Test different city endpoints
$endpoints = [
    '/destination/city',
    '/city',
    '/destination/city?province_id=5',
    '/city?province_id=5',
];

foreach ($endpoints as $endpoint) {
    echo 'Testing endpoint: '.$endpoint."\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://rajaongkir.komerce.id/api/v1'.$endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'key: '.$apiKey,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo 'HTTP Code: '.$httpCode."\n";
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
            echo 'SUCCESS! Found '.count($data['data'])." items\n";
        } else {
            echo 'Response: '.substr($response, 0, 200)."...\n";
        }
    } else {
        echo 'Response: '.substr($response, 0, 200)."...\n";
    }
    echo "\n";
}
