<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleMapsService;
use App\Exports\CompaniesExport;
use Maatwebsite\Excel\Facades\Excel;

class CompanySearchController extends Controller
{
    protected $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        $this->googleMapsService = $googleMapsService;
    }

    public function search(Request $request)
    {
        // DEBUG: İstek parametreleri
        \Log::info('=== ARAMA İSTEĞİ BAŞLADI ===');
        \Log::info('Request Data:', $request->all());
        
        $request->validate([
            'keyword' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'result_count' => 'required|integer|min:10|max:40',
        ]);

        $keyword = $request->input('keyword');
        $location = $request->input('location');
        $resultCount = $request->input('result_count');
        
        \Log::info('Validated Params:', [
            'keyword' => $keyword,
            'location' => $location,
            'result_count' => $resultCount
        ]);

        try {
            // Google Maps API ile arama yap
            \Log::info('GoogleMapsService çağrılıyor...');
            $results = $this->googleMapsService->searchCompanies($keyword, $location, $resultCount);
            \Log::info('Sonuç sayısı: ' . count($results));
            \Log::info('İlk sonuç:', $results[0] ?? 'Sonuç yok');
            
            // Session'a kaydet (export için)
            session(['search_results' => $results]);
            
            \Log::info('View\'e gönderiliyor...');
            \Log::info('=== ARAMA İSTEĞİ TAMAMLANDI ===');
            
            return view('results', [
                'results' => $results,
                'keyword' => $keyword,
                'location' => $location,
                'result_count' => $resultCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('ARAMA HATASI: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Arama sırasında bir hata oluştu: ' . $e->getMessage()]);
        }
    }

    public function export()
    {
        $results = session('search_results', []);
        
        if (empty($results)) {
            return back()->withErrors(['error' => 'Dışa aktarılacak veri bulunamadı. Lütfen önce bir arama yapın.']);
        }
        
        return Excel::download(new CompaniesExport($results), 'companies_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
