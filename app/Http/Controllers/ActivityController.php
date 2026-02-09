<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Etkinlik takibi sayfası
     */
    public function index(Request $request)
    {
        $companies = Company::with(['status', 'activities' => function($q) {
            $q->latest()->limit(5);
        }])
        ->withCount('activities')
        ->orderBy('updated_at', 'desc')
        ->get();

        $selectedCompany = null;
        $activities = collect();

        if ($request->filled('company_id')) {
            $selectedCompany = Company::with(['status', 'enrichedData'])
                ->findOrFail($request->company_id);
            
            $activities = Activity::where('company_id', $request->company_id)
                ->with('user')
                ->latest()
                ->paginate(20);
        }

        $activityTypes = Activity::TYPES;

        return view('activities.index', compact('companies', 'selectedCompany', 'activities', 'activityTypes'));
    }

    /**
     * Etkinlik ekle
     */
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

        // Firmanın updated_at'ini güncelle
        Company::find($request->company_id)->touch();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Etkinlik eklendi.']);
        }

        return back()->with('success', 'Etkinlik eklendi.');
    }

    /**
     * Etkinlik tamamla
     */
    public function complete(Activity $activity)
    {
        $activity->markAsCompleted();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Etkinlik tamamlandı.']);
        }

        return back()->with('success', 'Etkinlik tamamlandı.');
    }

    /**
     * Etkinlik sil
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Etkinlik silindi.']);
        }

        return back()->with('success', 'Etkinlik silindi.');
    }
}
