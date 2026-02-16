<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\CompanySearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| CRM Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Google Maps Search (Original Functionality)
Route::get('/search', function () {
    return view('search');
})->name('search');
Route::post('/search', [CompanySearchController::class, 'search'])->name('company.search');
Route::get('/search-export', [CompanySearchController::class, 'export'])->name('company.search.export');

// Companies (CRM)
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/export', [CompanyController::class, 'export'])->name('export');
    Route::post('/bulk-store', [CompanyController::class, 'bulkStore'])->name('bulk-store');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('show');
    Route::patch('/{company}/status', [CompanyController::class, 'updateStatus'])->name('update-status');
    Route::put('/{company}/enriched-data', [CompanyController::class, 'updateEnrichedData'])->name('enriched-data');
    Route::put('/{company}/notes', [CompanyController::class, 'updateNotes'])->name('notes');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
});

// Activities
Route::prefix('activities')->name('activities.')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('index');
    Route::post('/', [ActivityController::class, 'store'])->name('store');
    Route::patch('/{activity}/complete', [ActivityController::class, 'complete'])->name('complete');
    Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
});

// Map & Route
Route::prefix('maps')->name('maps.')->group(function () {
    Route::get('/', [MapController::class, 'index'])->name('index');
    Route::get('/navigation/{company}', [MapController::class, 'navigation'])->name('navigation');
});

// Reports
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

/*
|--------------------------------------------------------------------------
| API Routes (for AJAX calls)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    Route::post('/route/optimize', [MapController::class, 'optimizeRoute'])->name('api.route.optimize');
    
    // Hızlı arama API
    Route::get('/companies/search', function () {
        $query = request('q');
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $companies = \App\Models\Company::where('name', 'like', "%{$query}%")
            ->orWhere('address', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'address', 'phone']);
            
        return response()->json($companies);
    });
    
    // Son aktiviteler API (bildirimler için)
    Route::get('/recent-activities', function () {
        $activities = \App\Models\Activity::with('company:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'company_id' => $activity->company_id,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });
            
        return response()->json($activities);
    });
});

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
