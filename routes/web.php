<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\CompanySearchController;

Route::get('/', function () {
    return view('search');
})->name('home');

Route::get('/search', function () {
    return redirect()->route('home');
});

Route::post('/search', [CompanySearchController::class, 'search'])->name('company.search');
Route::get('/export', [CompanySearchController::class, 'export'])->name('company.export');

// API Key Test Route
Route::get('/test-api', function () {
    $apiKey = config('services.google_maps.api_key');
    
    // .env dosyasından direkt oku
    $envKey = env('GOOGLE_MAPS_API_KEY');
    
    if (!$apiKey) {
        return response()->json([
            'status' => 'error',
            'message' => 'API Key bulunamadı! .env dosyasını kontrol edin.',
            'config_key' => $apiKey,
            'env_key' => $envKey
        ]);
    }
    
    // Basit bir Geocoding API testi
    $testAddress = 'İstanbul, Türkiye';
    $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
        'address' => $testAddress,
        'key' => $apiKey,
    ]);
    
    $data = $response->json();
    
    if (isset($data['error_message'])) {
        return response()->json([
            'status' => 'error',
            'message' => 'API Key geçersiz veya yetkisiz!',
            'error' => $data['error_message'],
            'response' => $data
        ]);
    }
    
    if ($response->successful() && isset($data['results'][0])) {
        return response()->json([
            'status' => 'success',
            'message' => '✓ API Key başarıyla çalışıyor!',
            'config_api_key' => substr($apiKey, 0, 20) . '...',
            'env_api_key' => substr($envKey, 0, 20) . '...',
            'test_address' => $testAddress,
            'coordinates' => $data['results'][0]['geometry']['location'],
            'formatted_address' => $data['results'][0]['formatted_address']
        ]);
    }
    
    return response()->json([
        'status' => 'error',
        'message' => 'API çağrısı başarısız oldu',
        'response' => $data
    ]);
});
