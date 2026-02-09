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
            'companies_with_coordinates' => Company::withCoordinates()->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
