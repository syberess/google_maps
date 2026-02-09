# 🗺️ Google Maps CRM Sistemi - Kurulum Rehberi

Bu dokümantasyon, Google Maps tabanlı kapsamlı bir CRM sistemi kurulumu için adım adım rehber niteliğindedir.

---

## 📋 İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Sistem Mimarisi](#sistem-mimarisi)
3. [Veritabanı Yapısı](#veritabanı-yapısı)
4. [Model Yapıları](#model-yapıları)
5. [Controller Yapıları](#controller-yapıları)
6. [View Yapıları](#view-yapıları)
7. [Route Tanımlamaları](#route-tanımlamaları)
8. [Servis Sınıfları](#servis-sınıfları)
9. [JavaScript Entegrasyonları](#javascript-entegrasyonları)
10. [Kurulum Adımları](#kurulum-adımları)

---

## 🎯 Genel Bakış

### Sistem Özellikleri

| Özellik | Açıklama |
|---------|----------|
| **Google Maps Arama** | Kategori ve konum bazlı işletme arama |
| **Firma Yönetimi** | CRM tarzı firma kartları ve durum takibi |
| **Etkinlik Takibi** | Firma bazlı aktivite geçmişi |
| **Harita & Rota** | Leaflet.js ile harita görünümü ve optimum rota |
| **Veri Zenginleştirme** | Ek iletişim bilgileri toplama |
| **Excel Export** | Firma verilerini dışa aktarma |
| **Raporlar** | İstatistikler ve analizler |

### Teknoloji Stack

- **Backend:** Laravel 11+
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Harita:** Leaflet.js (OpenStreetMap)
- **Veritabanı:** MySQL / SQLite
- **API:** Google Maps Places API

---

## 🏗️ Sistem Mimarisi

```
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── CompanyController.php
│       ├── ActivityController.php
│       ├── MapController.php
│       ├── SearchController.php
│       └── ReportController.php
├── Models/
│   ├── Company.php
│   ├── CompanyStatus.php
│   ├── Activity.php
│   └── EnrichedData.php
├── Services/
│   ├── GoogleMapsService.php
│   ├── RouteOptimizationService.php
│   └── CompanyEnrichmentService.php
└── Exports/
    └── CompaniesExport.php

resources/views/
├── layouts/
│   └── app.blade.php
├── components/
│   ├── sidebar.blade.php
│   └── company-card.blade.php
├── dashboard.blade.php
├── companies/
│   ├── index.blade.php
│   └── show.blade.php
├── activities/
│   └── index.blade.php
├── maps/
│   └── index.blade.php
├── search.blade.php
└── reports/
    └── index.blade.php
```

---

## 🗄️ Veritabanı Yapısı

### 1. Companies Tablosu (Firmalar)

```php
// Migration: create_companies_table.php
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('google_place_id')->nullable()->unique();
    $table->string('phone')->nullable();
    $table->string('website')->nullable();
    $table->text('address')->nullable();
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->decimal('rating', 2, 1)->nullable();
    $table->integer('review_count')->default(0);
    $table->string('category')->nullable();
    $table->text('types')->nullable(); // JSON array
    $table->foreignId('status_id')->default(1)->constrained('company_statuses');
    $table->string('source')->default('google_maps'); // google_maps, manual
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index(['status_id', 'created_at']);
    $table->index('category');
});
```

### 2. Company Statuses Tablosu (Firma Durumları)

```php
// Migration: create_company_statuses_table.php
Schema::create('company_statuses', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Prospekt, Müzakere, Müşteri, Kayıp
    $table->string('slug')->unique();
    $table->string('color')->default('#6B7280'); // Tailwind gray-500
    $table->string('icon')->nullable();
    $table->integer('order')->default(0);
    $table->timestamps();
});
```

### 3. Activities Tablosu (Etkinlikler)

```php
// Migration: create_activities_table.php
Schema::create('activities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->onDelete('cascade');
    $table->string('type'); // search, call, meeting, proposal, note
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('status')->default('pending'); // pending, completed, cancelled
    $table->timestamp('scheduled_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->timestamps();
    
    $table->index(['company_id', 'created_at']);
    $table->index('type');
});
```

### 4. Enriched Data Tablosu (Zenginleştirilmiş Veriler)

```php
// Migration: create_enriched_data_table.php
Schema::create('enriched_data', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->onDelete('cascade');
    $table->string('email')->nullable();
    $table->string('secondary_phone')->nullable();
    $table->string('mobile_phone')->nullable();
    $table->string('fax')->nullable();
    $table->string('linkedin')->nullable();
    $table->string('facebook')->nullable();
    $table->string('instagram')->nullable();
    $table->string('twitter')->nullable();
    $table->string('contact_person')->nullable();
    $table->string('contact_title')->nullable();
    $table->text('additional_info')->nullable();
    $table->string('source')->nullable(); // manual, scraper, api
    $table->timestamps();
});
```

### Seeder - Varsayılan Durumlar

```php
// Seeder: CompanyStatusSeeder.php
$statuses = [
    [
        'name' => 'Prospekt',
        'slug' => 'prospekt',
        'color' => '#10B981', // green-500
        'icon' => 'circle',
        'order' => 1
    ],
    [
        'name' => 'Müzakere',
        'slug' => 'muzakere',
        'color' => '#F59E0B', // amber-500
        'icon' => 'clock',
        'order' => 2
    ],
    [
        'name' => 'Müşteri',
        'slug' => 'musteri',
        'color' => '#3B82F6', // blue-500
        'icon' => 'check-circle',
        'order' => 3
    ],
    [
        'name' => 'Kayıp',
        'slug' => 'kayip',
        'color' => '#EF4444', // red-500
        'icon' => 'x-circle',
        'order' => 4
    ],
];
```

---

## 📦 Model Yapıları

### Company Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'google_place_id',
        'phone',
        'website',
        'address',
        'latitude',
        'longitude',
        'rating',
        'review_count',
        'category',
        'types',
        'status_id',
        'source',
        'notes',
    ];

    protected $casts = [
        'types' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:1',
    ];

    // İlişkiler
    public function status()
    {
        return $this->belongsTo(CompanyStatus::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('created_at', 'desc');
    }

    public function enrichedData()
    {
        return $this->hasOne(EnrichedData::class);
    }

    // Scope'lar
    public function scopeByStatus($query, $status)
    {
        return $query->whereHas('status', fn($q) => $q->where('slug', $status));
    }

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // Accessor'lar
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_substr($word, 0, 1);
        }
        return mb_strtoupper($initials);
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return "https://www.google.com/maps/search/" . urlencode($this->address);
    }

    // Zengin veri kontrolü
    public function hasEnrichedData()
    {
        return $this->enrichedData !== null && 
               ($this->enrichedData->email || $this->enrichedData->secondary_phone);
    }
}
```

### CompanyStatus Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyStatus extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'icon', 'order'];

    public function companies()
    {
        return $this->hasMany(Company::class, 'status_id');
    }

    public function getCountAttribute()
    {
        return $this->companies()->count();
    }
}
```

### Activity Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'title',
        'description',
        'status',
        'scheduled_at',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Etkinlik tipleri
    const TYPES = [
        'search' => ['label' => 'Arama yapıldı', 'icon' => 'search', 'color' => 'blue'],
        'call' => ['label' => 'Telefon görüşmesi', 'icon' => 'phone', 'color' => 'green'],
        'meeting' => ['label' => 'Toplantı', 'icon' => 'users', 'color' => 'purple'],
        'proposal' => ['label' => 'Teklif gönderildi', 'icon' => 'document', 'color' => 'amber'],
        'note' => ['label' => 'Not', 'icon' => 'pencil', 'color' => 'gray'],
        'email' => ['label' => 'E-posta', 'icon' => 'mail', 'color' => 'indigo'],
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeInfoAttribute()
    {
        return self::TYPES[$this->type] ?? self::TYPES['note'];
    }
}
```

### EnrichedData Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrichedData extends Model
{
    protected $table = 'enriched_data';

    protected $fillable = [
        'company_id',
        'email',
        'secondary_phone',
        'mobile_phone',
        'fax',
        'linkedin',
        'facebook',
        'instagram',
        'twitter',
        'contact_person',
        'contact_title',
        'additional_info',
        'source',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function hasSocialMedia()
    {
        return $this->linkedin || $this->facebook || $this->instagram || $this->twitter;
    }
}
```

---

## 🎮 Controller Yapıları

### DashboardController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyStatus;
use App\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'by_status' => CompanyStatus::withCount('companies')
                ->orderBy('order')
                ->get(),
            'recent_activities' => Activity::with('company')
                ->latest()
                ->limit(10)
                ->get(),
            'recent_companies' => Company::with('status')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
```

### CompanyController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyStatus;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Exports\CompaniesExport;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['status', 'enrichedData', 'activities']);

        // Filtreleme
        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('slug', $request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sıralama
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortField, $sortDir);

        $companies = $query->paginate(20);
        $statuses = CompanyStatus::orderBy('order')->get();
        $categories = Company::distinct()->pluck('category')->filter();

        return view('companies.index', compact('companies', 'statuses', 'categories'));
    }

    public function show(Company $company)
    {
        $company->load(['status', 'enrichedData', 'activities.user']);
        $statuses = CompanyStatus::orderBy('order')->get();
        
        return view('companies.show', compact('company', 'statuses'));
    }

    public function updateStatus(Request $request, Company $company)
    {
        $request->validate(['status_id' => 'required|exists:company_statuses,id']);
        
        $oldStatus = $company->status->name;
        $company->update(['status_id' => $request->status_id]);
        $newStatus = $company->fresh()->status->name;

        // Aktivite kaydı
        Activity::create([
            'company_id' => $company->id,
            'type' => 'note',
            'title' => 'Durum değiştirildi',
            'description' => "{$oldStatus} → {$newStatus}",
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Durum güncellendi.');
    }

    public function storeFromSearch(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'google_place_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer',
            'category' => 'nullable|string',
            'types' => 'nullable|array',
        ]);

        // Duplicate kontrolü
        if (!empty($data['google_place_id'])) {
            $existing = Company::where('google_place_id', $data['google_place_id'])->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu firma zaten kayıtlı.',
                    'company' => $existing
                ], 409);
            }
        }

        $data['source'] = 'google_maps';
        $data['status_id'] = CompanyStatus::where('slug', 'prospekt')->first()->id ?? 1;

        $company = Company::create($data);

        // Arama aktivitesi ekle
        Activity::create([
            'company_id' => $company->id,
            'type' => 'search',
            'title' => 'Arama yapıldı',
            'description' => 'Google Maps aramasından eklendi',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Firma başarıyla eklendi.',
            'company' => $company->load('status')
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'companies' => 'required|array',
            'companies.*.name' => 'required|string',
        ]);

        $added = 0;
        $skipped = 0;

        foreach ($request->companies as $companyData) {
            if (!empty($companyData['google_place_id'])) {
                $exists = Company::where('google_place_id', $companyData['google_place_id'])->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
            }

            $companyData['source'] = 'google_maps';
            $companyData['status_id'] = CompanyStatus::where('slug', 'prospekt')->first()->id ?? 1;
            
            $company = Company::create($companyData);
            
            Activity::create([
                'company_id' => $company->id,
                'type' => 'search',
                'title' => 'Arama yapıldı',
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $added++;
        }

        return response()->json([
            'success' => true,
            'added' => $added,
            'skipped' => $skipped,
            'message' => "{$added} firma eklendi, {$skipped} firma zaten kayıtlıydı."
        ]);
    }

    public function export(Request $request)
    {
        $query = Company::with(['status', 'enrichedData']);

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('slug', $request->status));
        }

        $companies = $query->get();
        $filename = 'firmalar_' . date('Y-m-d_H-i-s');

        if ($request->format === 'csv') {
            return Excel::download(new CompaniesExport($companies), $filename . '.csv');
        }

        return Excel::download(new CompaniesExport($companies), $filename . '.xlsx');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return back()->with('success', 'Firma silindi.');
    }
}
```

### ActivityController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with(['status', 'activities' => function($q) {
            $q->latest()->limit(5);
        }])
        ->withCount('activities')
        ->orderBy('updated_at', 'desc')
        ->get();

        $selectedCompany = null;
        if ($request->filled('company_id')) {
            $selectedCompany = Company::with(['status', 'enrichedData', 'activities.user'])
                ->findOrFail($request->company_id);
        }

        return view('activities.index', compact('companies', 'selectedCompany'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|in:' . implode(',', array_keys(Activity::TYPES)),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $data['status'] = $request->filled('scheduled_at') ? 'pending' : 'completed';
        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        Activity::create($data);

        return back()->with('success', 'Etkinlik eklendi.');
    }

    public function complete(Activity $activity)
    {
        $activity->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Etkinlik tamamlandı.');
    }
}
```

### MapController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\RouteOptimizationService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    protected $routeService;

    public function __construct(RouteOptimizationService $routeService)
    {
        $this->routeService = $routeService;
    }

    public function index()
    {
        $companies = Company::withCoordinates()
            ->with('status')
            ->get();

        return view('maps.index', compact('companies'));
    }

    public function optimizeRoute(Request $request)
    {
        $request->validate([
            'company_ids' => 'required|array|min:2',
            'company_ids.*' => 'exists:companies,id',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
        ]);

        $companies = Company::whereIn('id', $request->company_ids)
            ->withCoordinates()
            ->get();

        $startPoint = [
            'lat' => $request->start_lat,
            'lng' => $request->start_lng,
        ];

        $optimizedRoute = $this->routeService->optimize($startPoint, $companies);

        return response()->json([
            'success' => true,
            'route' => $optimizedRoute,
            'total_distance' => $optimizedRoute['total_distance'],
        ]);
    }
}
```

### SearchController (Güncellenmiş)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleMapsService;

class SearchController extends Controller
{
    protected $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        $this->googleMapsService = $googleMapsService;
    }

    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'result_count' => 'required|integer|min:10|max:100',
        ]);

        try {
            $results = $this->googleMapsService->searchCompanies(
                $request->keyword,
                $request->location,
                $request->result_count
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'results' => $results,
                    'count' => count($results),
                ]);
            }

            session(['search_results' => $results]);

            return view('search', [
                'results' => $results,
                'keyword' => $request->keyword,
                'location' => $request->location,
            ]);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

---

## 🎨 View Yapıları

### Ana Layout (layouts/app.blade.php)

```blade
<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GMaps CRM')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">
                        @yield('page-title')
                    </h1>
                    <div class="flex items-center space-x-4">
                        @yield('header-actions')
                        
                        <!-- User Menu -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                {{ auth()->user()->name ?? 'Kullanıcı' }}
                            </span>
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @stack('scripts')
</body>
</html>
```

### Sidebar Component (components/sidebar.blade.php)

```blade
<aside class="fixed left-0 top-0 h-full w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-50">
    <!-- Logo -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 dark:text-white">GMaps</h2>
                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded">PRO Edition</span>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="p-4 space-y-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Ana Menü</p>
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('search') }}" 
           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('search*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Arama Yap</span>
        </a>
        
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-3">CRM</p>
        
        <a href="{{ route('companies.index') }}" 
           class="flex items-center justify-between px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('companies*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Firmalar</span>
            </div>
            <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                {{ \App\Models\Company::count() }}
            </span>
        </a>
        
        <a href="{{ route('activities.index') }}" 
           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('activities*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Etkinlikler</span>
        </a>
        
        <a href="{{ route('maps.index') }}" 
           class="flex items-center justify-between px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('maps*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span>Harita & Rota</span>
            </div>
            <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                {{ \App\Models\Company::withCoordinates()->count() }}
            </span>
        </a>
        
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-3">Analiz</p>
        
        <a href="{{ route('reports.index') }}" 
           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition
                  {{ request()->routeIs('reports*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/50' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Raporlar</span>
        </a>
    </nav>
    
    <!-- Bottom Section -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <!-- Quick Stats -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Hızlı Erişim</p>
            <div class="grid grid-cols-2 gap-2">
                <div class="text-center">
                    <div class="text-lg font-bold text-blue-600">
                        {{ \App\Models\Company::byStatus('musteri')->count() }}
                    </div>
                    <div class="text-xs text-gray-500">Müşteri</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-amber-500">
                        {{ \App\Models\Company::byStatus('muzakere')->count() }}
                    </div>
                    <div class="text-xs text-gray-500">Müzakere</div>
                </div>
            </div>
        </div>
        
        <!-- Dark Mode Toggle -->
        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-text="darkMode ? 'Açık Tema' : 'Koyu Tema'"></span>
        </button>
    </div>
</aside>
```

### Firma Kartı Component (components/company-card.blade.php)

```blade
@props(['company'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition">
    <!-- Header -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                 style="background-color: {{ $company->status->color }}">
                {{ $company->initials }}
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 dark:text-white">
                    {{ Str::limit($company->name, 30) }}
                </h3>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-0.5 rounded-full" 
                          style="background-color: {{ $company->status->color }}20; color: {{ $company->status->color }}">
                        {{ $company->status->name }}
                    </span>
                    @if($company->rating)
                        <span class="flex items-center text-amber-500">
                            <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $company->rating }}
                        </span>
                    @endif
                    @if($company->hasEnrichedData())
                        <span class="text-green-500" title="Zenginleştirilmiş Veri">✓ Zengin</span>
                    @endif
                    @if($company->source === 'google_maps')
                        <span class="text-green-600">◆ GMaps</span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Actions Menu -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
            </button>
            <div x-show="open" @click.away="open = false" 
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-10">
                <a href="{{ route('companies.show', $company) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Detay Görüntüle
                </a>
                <a href="{{ $company->google_maps_url }}" target="_blank"
                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Google Maps'te Aç
                </a>
            </div>
        </div>
    </div>
    
    <!-- Category -->
    @if($company->category)
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $company->category }}</p>
    @endif
    
    <!-- Contact Info -->
    <div class="space-y-2 text-sm">
        @if($company->phone)
            <a href="tel:{{ $company->phone }}" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>{{ $company->phone }}</span>
            </a>
        @endif
        
        @if($company->website)
            <a href="{{ $company->website }}" target="_blank" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                <span class="truncate">{{ parse_url($company->website, PHP_URL_HOST) }}</span>
            </a>
        @endif
        
        @if($company->address)
            <div class="flex items-start space-x-2 text-gray-600 dark:text-gray-300">
                <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                <span class="text-xs">{{ Str::limit($company->address, 60) }}</span>
            </div>
        @endif
    </div>
    
    <!-- Enriched Data Section -->
    @if($company->hasEnrichedData())
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <button class="text-xs text-purple-600 hover:text-purple-800 flex items-center space-x-1"
                    x-data="{ open: false }" @click="open = !open">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>Zenginleştirilmiş Veriler</span>
            </button>
            <div x-show="open" class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                @if($company->enrichedData->email)
                    <div class="flex items-center space-x-1">
                        <span>📧</span>
                        <a href="mailto:{{ $company->enrichedData->email }}" class="hover:text-blue-600">
                            {{ $company->enrichedData->email }}
                        </a>
                    </div>
                @endif
                @if($company->enrichedData->secondary_phone)
                    <div class="flex items-center space-x-1">
                        <span>📱</span>
                        <span>{{ $company->enrichedData->secondary_phone }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Last Activity -->
    @if($company->activities->count() > 0)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>⏱️ Son Etkinlikler</span>
                <span>{{ $company->activities->first()->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                {{ $company->activities->first()->title }}
            </p>
        </div>
    @endif
    
    <!-- Status Selector -->
    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
        <form action="{{ route('companies.update-status', $company) }}" method="POST" class="flex items-center space-x-2">
            @csrf
            @method('PATCH')
            <select name="status_id" onchange="this.form.submit()"
                    class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-blue-500">
                @foreach(\App\Models\CompanyStatus::orderBy('order')->get() as $status)
                    <option value="{{ $status->id }}" {{ $company->status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
            
            <!-- Quick Actions -->
            <button type="button" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Ayarlar">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
            <button type="button" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Favorilere Ekle">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </button>
        </form>
    </div>
</div>
```

---

## 🛣️ Route Tanımlamaları

```php
<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReportController;

// Ana Sayfa
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Arama
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/search', [SearchController::class, 'search'])->name('search.perform');

// Firmalar
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('show');
    Route::patch('/{company}/status', [CompanyController::class, 'updateStatus'])->name('update-status');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
    Route::get('/export/{format?}', [CompanyController::class, 'export'])->name('export');
});

// Etkinlikler
Route::prefix('activities')->name('activities.')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('index');
    Route::post('/', [ActivityController::class, 'store'])->name('store');
    Route::patch('/{activity}/complete', [ActivityController::class, 'complete'])->name('complete');
});

// Harita
Route::prefix('maps')->name('maps.')->group(function () {
    Route::get('/', [MapController::class, 'index'])->name('index');
    Route::post('/optimize-route', [MapController::class, 'optimizeRoute'])->name('optimize-route');
});

// Raporlar
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// API Endpoints
Route::prefix('api')->group(function () {
    Route::post('/companies/store-from-search', [CompanyController::class, 'storeFromSearch'])
        ->name('api.companies.store-from-search');
    Route::post('/companies/bulk-store', [CompanyController::class, 'bulkStore'])
        ->name('api.companies.bulk-store');
    Route::post('/route/optimize', [MapController::class, 'optimizeRoute'])
        ->name('api.route.optimize');
});
```

---

## ⚙️ Servis Sınıfları

### RouteOptimizationService

```php
<?php

namespace App\Services;

use Illuminate\Support\Collection;

class RouteOptimizationService
{
    /**
     * Nearest Neighbor algoritması ile rota optimizasyonu
     */
    public function optimize(array $startPoint, Collection $companies): array
    {
        if ($companies->isEmpty()) {
            return ['route' => [], 'total_distance' => 0];
        }

        $route = [];
        $totalDistance = 0;
        $unvisited = $companies->all();
        $currentPoint = $startPoint;

        while (!empty($unvisited)) {
            $nearestIndex = $this->findNearest($currentPoint, $unvisited);
            $nearest = $unvisited[$nearestIndex];
            
            $distance = $this->calculateDistance(
                $currentPoint['lat'], $currentPoint['lng'],
                $nearest->latitude, $nearest->longitude
            );
            
            $route[] = [
                'company' => $nearest,
                'distance_from_previous' => round($distance, 2),
            ];
            
            $totalDistance += $distance;
            $currentPoint = ['lat' => $nearest->latitude, 'lng' => $nearest->longitude];
            
            array_splice($unvisited, $nearestIndex, 1);
        }

        return [
            'route' => $route,
            'total_distance' => round($totalDistance, 2),
            'start_point' => $startPoint,
        ];
    }

    /**
     * En yakın noktayı bul
     */
    protected function findNearest(array $point, array $candidates): int
    {
        $minDistance = PHP_FLOAT_MAX;
        $nearestIndex = 0;

        foreach ($candidates as $index => $candidate) {
            $distance = $this->calculateDistance(
                $point['lat'], $point['lng'],
                $candidate->latitude, $candidate->longitude
            );
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestIndex = $index;
            }
        }

        return $nearestIndex;
    }

    /**
     * Haversine formülü ile mesafe hesapla (km)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
```

---

## 📜 JavaScript Entegrasyonları

### Harita Yönetimi (resources/js/map.js)

```javascript
// Harita ve Rota Yönetimi
class MapManager {
    constructor(containerId, options = {}) {
        this.map = L.map(containerId).setView(
            options.center || [39.9334, 32.8597], // Türkiye merkezi
            options.zoom || 6
        );
        
        this.markers = [];
        this.routeLine = null;
        
        // OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);
    }

    // Firma markerları ekle
    addCompanyMarkers(companies) {
        companies.forEach(company => {
            if (company.latitude && company.longitude) {
                const marker = L.marker([company.latitude, company.longitude])
                    .addTo(this.map)
                    .bindPopup(`
                        <div class="p-2">
                            <h3 class="font-bold">${company.name}</h3>
                            ${company.phone ? `<p>📞 ${company.phone}</p>` : ''}
                            ${company.address ? `<p>📍 ${company.address}</p>` : ''}
                            <div class="mt-2">
                                <a href="/companies/${company.id}" class="text-blue-600 text-sm">
                                    Detay →
                                </a>
                            </div>
                        </div>
                    `);
                
                marker.companyId = company.id;
                this.markers.push(marker);
            }
        });

        // Tüm markerları görecek şekilde zoom ayarla
        if (this.markers.length > 0) {
            const group = L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Rota çiz
    drawRoute(routeData) {
        // Önceki rotayı temizle
        if (this.routeLine) {
            this.map.removeLayer(this.routeLine);
        }

        const points = [
            [routeData.start_point.lat, routeData.start_point.lng],
            ...routeData.route.map(item => [item.company.latitude, item.company.longitude])
        ];

        this.routeLine = L.polyline(points, {
            color: '#3B82F6',
            weight: 3,
            opacity: 0.7,
            dashArray: '10, 10'
        }).addTo(this.map);

        // Numaralı markerlar ekle
        routeData.route.forEach((item, index) => {
            L.marker([item.company.latitude, item.company.longitude], {
                icon: L.divIcon({
                    className: 'route-number-marker',
                    html: `<div class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">${index + 1}</div>`
                })
            }).addTo(this.map);
        });
    }

    // Konum al
    getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject('Geolocation desteklenmiyor');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                }),
                error => reject(error.message)
            );
        });
    }

    // Rota optimizasyonu
    async optimizeRoute(companyIds, startLat, startLng) {
        const response = await fetch('/api/route/optimize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                company_ids: companyIds,
                start_lat: startLat,
                start_lng: startLng
            })
        });

        return response.json();
    }
}

// Export for use
window.MapManager = MapManager;
```

### Firma Ekleme (resources/js/company-save.js)

```javascript
// Aramadan firma kaydetme işlemleri
class CompanySaver {
    constructor() {
        this.savedCompanies = new Set();
    }

    async saveCompany(companyData) {
        if (this.savedCompanies.has(companyData.google_place_id)) {
            return { success: false, message: 'Bu firma zaten eklendi.' };
        }

        const response = await fetch('/api/companies/store-from-search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(companyData)
        });

        const result = await response.json();
        
        if (result.success) {
            this.savedCompanies.add(companyData.google_place_id);
        }

        return result;
    }

    async bulkSave(companies) {
        const response = await fetch('/api/companies/bulk-store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ companies })
        });

        return response.json();
    }

    isAlreadySaved(placeId) {
        return this.savedCompanies.has(placeId);
    }
}

window.CompanySaver = CompanySaver;
```

---

## 🚀 Kurulum Adımları

### 1. Veritabanı Migrations Oluştur

```bash
php artisan make:migration create_company_statuses_table
php artisan make:migration create_companies_table
php artisan make:migration create_activities_table
php artisan make:migration create_enriched_data_table
```

### 2. Migration Dosyalarını Düzenle
Yukarıdaki veritabanı yapısı bölümündeki kodları ilgili migration dosyalarına ekleyin.

### 3. Migration'ları Çalıştır

```bash
php artisan migrate
```

### 4. Seeder Oluştur

```bash
php artisan make:seeder CompanyStatusSeeder
```

### 5. Seeder'ı Çalıştır

```bash
php artisan db:seed --class=CompanyStatusSeeder
```

### 6. Model'leri Oluştur

```bash
php artisan make:model Company
php artisan make:model CompanyStatus
php artisan make:model Activity
php artisan make:model EnrichedData
```

### 7. Controller'ları Oluştur

```bash
php artisan make:controller DashboardController
php artisan make:controller CompanyController
php artisan make:controller ActivityController
php artisan make:controller MapController
php artisan make:controller SearchController
php artisan make:controller ReportController
```

### 8. Service Oluştur

```bash
mkdir app/Services
# RouteOptimizationService.php dosyasını oluştur
```

### 9. View Klasörlerini Oluştur

```bash
mkdir resources/views/layouts
mkdir resources/views/components
mkdir resources/views/companies
mkdir resources/views/activities
mkdir resources/views/maps
mkdir resources/views/reports
```

### 10. Tailwind CSS Konfigürasyonu

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### 11. Assets'leri Derle

```bash
npm install
npm run build
```

### 12. Cache Temizle

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## ✅ Kontrol Listesi

- [ ] Migrations oluşturuldu ve çalıştırıldı
- [ ] Seeder ile varsayılan durumlar eklendi
- [ ] Tüm Model'ler oluşturuldu
- [ ] Tüm Controller'lar oluşturuldu
- [ ] Route'lar tanımlandı
- [ ] Layout ve Sidebar oluşturuldu
- [ ] Firma kartı componenti oluşturuldu
- [ ] Tüm View'lar oluşturuldu
- [ ] JavaScript dosyaları oluşturuldu
- [ ] Tailwind CSS yapılandırıldı
- [ ] Assets derlendi

---

## 📝 Notlar

1. **Google API Key:** .env dosyasında `GOOGLE_MAPS_API_KEY` tanımlı olmalı
2. **Veritabanı:** MySQL veya SQLite kullanılabilir
3. **Authentication:** Laravel Breeze veya Jetstream entegrasyonu opsiyonel
4. **Leaflet.js:** Ücretsiz ve açık kaynak harita kütüphanesi kullanılıyor

---

*Bu dokümantasyon, Google Maps CRM sisteminin tam kurulum rehberidir. Adımları sırayla takip ederek sistemin tamamını kurabilirsiniz.*
