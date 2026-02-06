<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CONFIG CHECK ===\n";
echo "Config API Key: " . config('services.google_maps.api_key') . "\n";
echo "ENV API Key: " . env('GOOGLE_MAPS_API_KEY') . "\n";
echo "ENV File: " . base_path('.env') . "\n";
echo "Exists: " . (file_exists(base_path('.env')) ? 'YES' : 'NO') . "\n";
