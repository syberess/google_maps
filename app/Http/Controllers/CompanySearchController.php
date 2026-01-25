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
        $request->validate([
            'keyword' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'radius' => 'required|integer|min:100|max:50000',
        ]);

        $keyword = $request->input('keyword');
        $location = $request->input('location');
        $radius = $request->input('radius');

        try {
            // Google Maps API ile arama yap
            $results = $this->googleMapsService->searchCompanies($keyword, $location, $radius);
            
            // Session'a kaydet (export için)
            session(['search_results' => $results]);
            
            return view('results', [
                'results' => $results,
                'keyword' => $keyword,
                'location' => $location,
                'radius' => $radius
            ]);
            
        } catch (\Exception $e) {
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
