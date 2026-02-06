<?php

// API Key Test Script
$apiKey = 'AIzaSyCTb1iZfFVHjUgdvLWNe16JYH4XH2ZDYBA';

echo "=================================\n";
echo "Google Maps API Key Test\n";
echo "=================================\n\n";

// Test 1: Geocoding API
echo "Test 1: Geocoding API (İstanbul, Türkiye)\n";
echo "-------------------------------------------\n";

$testAddress = urlencode('İstanbul, Türkiye');
$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$testAddress}&key={$apiKey}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['error_message'])) {
    echo "❌ HATA: " . $data['error_message'] . "\n";
    echo "Status: " . $data['status'] . "\n";
} elseif (isset($data['results'][0])) {
    echo "✓ Başarılı!\n";
    echo "Adres: " . $data['results'][0]['formatted_address'] . "\n";
    echo "Koordinatlar: \n";
    echo "  Enlem: " . $data['results'][0]['geometry']['location']['lat'] . "\n";
    echo "  Boylam: " . $data['results'][0]['geometry']['location']['lng'] . "\n";
} else {
    echo "❌ Beklenmeyen yanıt:\n";
    echo $response . "\n";
}

echo "\n";

// Test 2: Places API - Nearby Search
echo "Test 2: Places API - Nearby Search\n";
echo "-------------------------------------------\n";

$lat = 41.0082;
$lng = 28.9784;
$radius = 5000;
$keyword = 'restaurant';

$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$lat},{$lng}&radius={$radius}&keyword={$keyword}&key={$apiKey}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['error_message'])) {
    echo "❌ HATA: " . $data['error_message'] . "\n";
    echo "Status: " . $data['status'] . "\n";
} elseif (isset($data['results']) && count($data['results']) > 0) {
    echo "✓ Başarılı!\n";
    echo "Bulunan yer sayısı: " . count($data['results']) . "\n";
    echo "İlk 3 sonuç:\n";
    for ($i = 0; $i < min(3, count($data['results'])); $i++) {
        echo "  " . ($i + 1) . ". " . $data['results'][$i]['name'] . "\n";
    }
} else {
    echo "⚠ Sonuç bulunamadı veya beklenmeyen yanıt\n";
    echo "Status: " . ($data['status'] ?? 'Unknown') . "\n";
}

echo "\n";

// Test 3: API Quota Check
echo "Test 3: API Durumu\n";
echo "-------------------------------------------\n";
echo "API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "\n";

echo "=================================\n";
echo "Test Tamamlandı!\n";
echo "=================================\n";
