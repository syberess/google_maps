<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyStatus;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Raporlar sayfası
     */
    public function index(Request $request)
    {
        // Duruma göre dağılım
        $statusDistribution = CompanyStatus::withCount('companies')
            ->orderBy('order')
            ->get();

        // by_status için slug -> count mapping
        $byStatus = $statusDistribution->mapWithKeys(function ($status) {
            return [$status->slug => $status->companies_count];
        })->toArray();

        // by_activity_type için type -> count mapping
        $byActivityType = Activity::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => $item->count];
            })->toArray();

        // Genel istatistikler
        $stats = [
            'total_companies' => Company::count(),
            'companies_with_phone' => Company::whereNotNull('phone')->count(),
            'companies_with_website' => Company::whereNotNull('website')->count(),
            'companies_with_coordinates' => Company::withCoordinates()->count(),
            'total_activities' => Activity::count(),
            'pending_activities' => Activity::where('status', 'pending')->count(),
            'new_this_month' => Company::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'by_status' => $byStatus,
            'by_activity_type' => $byActivityType,
        ];

        // Kategoriye göre dağılım
        $categoryDistribution = Company::select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Aylık eklenen firmalar (son 6 ay)
        $monthlyCompanies = Company::select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Etkinlik tipleri dağılımı
        $activityTypeDistribution = Activity::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function($item) {
                $typeInfo = Activity::TYPES[$item->type] ?? ['label' => $item->type];
                return [$typeInfo['label'] => $item->count];
            });

        // En yüksek puanlı firmalar
        $topRatedCompanies = Company::whereNotNull('rating')
            ->with('status')
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->limit(10)
            ->get();

        // Son eklenen firmalar
        $recentCompanies = Company::with('status')
            ->latest()
            ->limit(10)
            ->get();

        // Son etkinlikler (7 gün)
        $recentActivities = Activity::with('company')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'stats',
            'statusDistribution',
            'categoryDistribution',
            'monthlyCompanies',
            'activityTypeDistribution',
            'topRatedCompanies',
            'recentCompanies',
            'recentActivities'
        ));
    }
}
