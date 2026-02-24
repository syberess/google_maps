<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyStatus;
use App\Models\Activity;
use App\Models\EnrichedData;
use Illuminate\Http\Request;
use App\Exports\CompaniesExport;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    /**
     * Firma listesi
     */
    public function index(Request $request)
    {
        $query = Company::with(['status', 'enrichedData', 'activities' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Filtreleme
        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('slug', $request->status));
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sıralama
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortField, $sortDir);

        $companies = $query->paginate(18)->withQueryString();
        $statuses = CompanyStatus::orderBy('order')->get();
        $categories = Company::distinct()->pluck('category')->filter()->values();

        // İstatistikler
        $statusCounts = CompanyStatus::withCount('companies')->orderBy('order')->get();

        return view('companies.index', compact('companies', 'statuses', 'categories', 'statusCounts'));
    }

    /**
     * Firma detayı
     */
    public function show(Company $company)
    {
        $company->load(['status', 'enrichedData', 'activities.user']);
        $statuses = CompanyStatus::orderBy('order')->get();
        
        return view('companies.show', compact('company', 'statuses'));
    }

    /**
     * Durum güncelleme
     */
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Durum güncellendi.']);
        }

        return back()->with('success', 'Durum güncellendi.');
    }

    /**
     * Arama sonuçlarından firma kaydet
     */
    public function storeFromSearch(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'google_place_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'website' => 'nullable|string',
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
        $data['status_id'] = CompanyStatus::getDefault()->id ?? 1;

        $company = Company::create($data);

        // Otomatik etkinlik oluşturmuyoruz - kullanıcı gerçek aktivite yaptığında kendisi ekleyecek

        return response()->json([
            'success' => true,
            'message' => 'Firma başarıyla eklendi.',
            'company' => $company->load('status')
        ]);
    }

    /**
     * Toplu firma kaydet
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'companies' => 'required|array',
            'companies.*.name' => 'required|string',
        ]);

        $added = 0;
        $skipped = 0;
        $defaultStatusId = CompanyStatus::getDefault()->id ?? 1;

        // Google Maps tip -> Türkçe kategori eşlemesi
        $categoryMap = [
            'restaurant' => 'Restoran',
            'cafe' => 'Kafe',
            'bar' => 'Bar',
            'bakery' => 'Fırın',
            'meal_takeaway' => 'Paket Servis',
            'meal_delivery' => 'Yemek Teslimat',
            'food' => 'Yemek',
            'store' => 'Mağaza',
            'clothing_store' => 'Giyim Mağazası',
            'electronics_store' => 'Elektronik Mağazası',
            'hardware_store' => 'Yapı Market',
            'home_goods_store' => 'Ev Eşyası',
            'furniture_store' => 'Mobilya',
            'jewelry_store' => 'Kuyumcu',
            'shoe_store' => 'Ayakkabı Mağazası',
            'shopping_mall' => 'AVM',
            'supermarket' => 'Süpermarket',
            'convenience_store' => 'Market',
            'grocery_or_supermarket' => 'Market',
            'pharmacy' => 'Eczane',
            'hospital' => 'Hastane',
            'doctor' => 'Doktor',
            'dentist' => 'Diş Hekimi',
            'veterinary_care' => 'Veteriner',
            'gym' => 'Spor Salonu',
            'spa' => 'Spa',
            'beauty_salon' => 'Güzellik Salonu',
            'hair_care' => 'Kuaför',
            'bank' => 'Banka',
            'atm' => 'ATM',
            'accounting' => 'Muhasebe',
            'insurance_agency' => 'Sigorta',
            'lawyer' => 'Avukat',
            'real_estate_agency' => 'Emlak',
            'travel_agency' => 'Seyahat Acentesi',
            'lodging' => 'Konaklama',
            'hotel' => 'Otel',
            'car_dealer' => 'Oto Galeri',
            'car_repair' => 'Oto Tamir',
            'car_wash' => 'Oto Yıkama',
            'gas_station' => 'Benzin İstasyonu',
            'parking' => 'Otopark',
            'school' => 'Okul',
            'university' => 'Üniversite',
            'library' => 'Kütüphane',
            'mosque' => 'Cami',
            'church' => 'Kilise',
            'park' => 'Park',
            'movie_theater' => 'Sinema',
            'night_club' => 'Gece Kulübü',
            'casino' => 'Kumarhane',
            'laundry' => 'Çamaşırhane',
            'locksmith' => 'Çilingir',
            'plumber' => 'Tesisatçı',
            'electrician' => 'Elektrikçi',
            'florist' => 'Çiçekçi',
            'pet_store' => 'Pet Shop',
        ];

        foreach ($request->companies as $companyData) {
            // place_id veya google_place_id'yi al
            $placeId = $companyData['place_id'] ?? $companyData['google_place_id'] ?? null;
            
            if (!empty($placeId)) {
                $exists = Company::where('google_place_id', $placeId)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
            }

            // Kategoriyi parse et
            $rawTypes = $companyData['types'] ?? $companyData['category'] ?? '';
            $category = null;
            
            if (!empty($rawTypes)) {
                $typesArray = is_array($rawTypes) ? $rawTypes : explode(',', $rawTypes);
                $typesArray = array_map('trim', $typesArray);
                
                // Genel tipleri çıkar
                $excludeTypes = ['establishment', 'point_of_interest', 'food', 'place', 'business'];
                
                foreach ($typesArray as $type) {
                    $type = strtolower(trim($type));
                    if (in_array($type, $excludeTypes)) continue;
                    
                    if (isset($categoryMap[$type])) {
                        $category = $categoryMap[$type];
                        break;
                    }
                }
                
                // Eşleşme yoksa ilk anlamlı tipi kullan
                if (!$category) {
                    foreach ($typesArray as $type) {
                        $type = strtolower(trim($type));
                        if (!in_array($type, $excludeTypes)) {
                            $category = ucfirst($type);
                            break;
                        }
                    }
                }
            }

            // Veri formatını normalize et (Google Maps aramasından gelen format)
            $company = Company::create([
                'name' => $companyData['name'],
                'google_place_id' => $placeId,
                'phone' => $companyData['phone'] ?? null,
                'website' => $companyData['website'] ?? null,
                'address' => $companyData['address'] ?? $companyData['formatted_address'] ?? null,
                'latitude' => $companyData['latitude'] ?? null,
                'longitude' => $companyData['longitude'] ?? null,
                'rating' => $companyData['rating'] ?? null,
                'review_count' => $companyData['total_ratings'] ?? $companyData['review_count'] ?? 0,
                'category' => $category,
                'source' => 'google_maps',
                'status_id' => $defaultStatusId,
            ]);
            
            // Firma ekleme notu (isteğe bağlı - otomatik etkinlik oluşturmuyoruz)
            // Kullanıcı gerçek bir aktivite yaptığında kendisi ekleyecek

            $added++;
        }

        return response()->json([
            'success' => true,
            'added' => $added,
            'skipped' => $skipped,
            'message' => "{$added} firma eklendi, {$skipped} firma zaten kayıtlıydı."
        ]);
    }

    /**
     * Zenginleştirilmiş veri kaydet
     */
    public function storeEnrichedData(Request $request, Company $company)
    {
        $data = $request->validate([
            'email' => 'nullable|email',
            'secondary_phone' => 'nullable|string',
            'mobile_phone' => 'nullable|string',
            'linkedin' => 'nullable|url',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|string',
            'twitter' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'contact_title' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        $data['source'] = 'manual';

        $company->enrichedData()->updateOrCreate(
            ['company_id' => $company->id],
            $data
        );

        return back()->with('success', 'Zenginleştirilmiş veriler kaydedildi.');
    }

    /**
     * Excel/CSV export
     */
    public function export(Request $request)
    {
        $query = Company::with(['status', 'enrichedData']);

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('slug', $request->status));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $companies = $query->get();
        $filename = 'firmalar_' . date('Y-m-d_H-i-s');
        $format = $request->query('format', 'xlsx');

        if ($format === 'csv') {
            return Excel::download(new CompaniesExport($companies, true), $filename . '.csv');
        }

        return Excel::download(new CompaniesExport($companies, true), $filename . '.xlsx');
    }

    /**
     * Firma sil
     */
    public function destroy(Company $company)
    {
        $company->delete();
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Firma silindi.']);
        }

        return back()->with('success', 'Firma silindi.');
    }

    /**
     * Not güncelle
     */
    public function updateNotes(Request $request, Company $company)
    {
        $request->validate(['notes' => 'nullable|string']);
        
        $company->update(['notes' => $request->notes]);

        return back()->with('success', 'Notlar güncellendi.');
    }
}
