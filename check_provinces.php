<?php

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

$apiKey = $_ENV['RAJAONGKIR_API_KEY'] ?? '';
$baseUrl = 'https://rajaongkir.komerce.id/api/v1';

echo "Checking provinces...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$baseUrl}/destination/province");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    "key: {$apiKey}",
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);

    if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
        echo 'Provinces found ('.count($data['data'])."):\n";
        foreach ($data['data'] as $province) {
            echo '- '.($province['province'] ?? '').' (ID: '.($province['province_id'] ?? '').")\n";
        }
    } else {
        echo "Error retrieving provinces:\n";
        print_r($data);
    }
} else {
    echo 'HTTP Error: '.$httpCode."\n";
    echo 'Response: '.$response."\n";
}
