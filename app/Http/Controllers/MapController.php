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

    /**
     * Harita ve rota planlama sayfası
     */
    public function index(Request $request)
    {
        $companies = Company::withCoordinates()
            ->with('status')
            ->get();

        $selectedCompanyIds = $request->get('selected', []);
        if (is_string($selectedCompanyIds)) {
            $selectedCompanyIds = explode(',', $selectedCompanyIds);
        }

        return view('maps.index', compact('companies', 'selectedCompanyIds'));
    }

    /**
     * Rota optimizasyonu
     */
    public function optimizeRoute(Request $request)
    {
        $request->validate([
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'exists:companies,id',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
        ]);

        $companies = Company::whereIn('id', $request->company_ids)
            ->withCoordinates()
            ->get();

        $startPoint = [
            'lat' => (float) $request->start_lat,
            'lng' => (float) $request->start_lng,
        ];

        $optimizedRoute = $this->routeService->optimize($startPoint, $companies);

        return response()->json([
            'success' => true,
            'route' => $optimizedRoute['route'],
            'total_distance' => $optimizedRoute['total_distance'],
            'start_point' => $optimizedRoute['start_point'],
        ]);
    }

    /**
     * Google Maps navigasyonu için URL oluştur
     */
    public function getNavigationUrl(Request $request)
    {
        $request->validate([
            'company_ids' => 'required|array|min:1',
        ]);

        $companies = Company::whereIn('id', $request->company_ids)
            ->withCoordinates()
            ->get();

        if ($companies->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Koordinatlı firma bulunamadı.']);
        }

        // Google Maps directions URL oluştur
        $waypoints = $companies->map(function($company) {
            return "{$company->latitude},{$company->longitude}";
        })->toArray();

        $destination = array_pop($waypoints);
        $waypointsStr = implode('|', $waypoints);

        $url = "https://www.google.com/maps/dir/?api=1&destination={$destination}";
        if (!empty($waypoints)) {
            $url .= "&waypoints=" . urlencode($waypointsStr);
        }

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    }
}
