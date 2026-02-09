@extends('layouts.app')

@section('title', 'Raporlar - GMaps CRM')
@section('page-title', 'Raporlar & İstatistikler')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Toplam Firma</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_companies'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600">+{{ $stats['new_this_month'] }}</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">bu ay eklendi</span>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Toplam Etkinlik</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_activities'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-amber-600">{{ $stats['pending_activities'] }}</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">bekleyende</span>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Müşteri Oranı</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $stats['total_companies'] > 0 ? round(($stats['by_status']['musteri'] ?? 0) / $stats['total_companies'] * 100) : 0 }}%
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ $stats['by_status']['musteri'] ?? 0 }} aktif müşteri</span>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Kayıp Oranı</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $stats['total_companies'] > 0 ? round(($stats['by_status']['kayip'] ?? 0) / $stats['total_companies'] * 100) : 0 }}%
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ $stats['by_status']['kayip'] ?? 0 }} kayıp firma</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-white">Durum Dağılımı</h3>
                <a href="{{ route('companies.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Tümünü Gör →</a>
            </div>
            <div class="p-6">
                @foreach($statusDistribution as $status)
                @php
                    $count = $status->companies_count;
                    $percentage = $stats['total_companies'] > 0 ? ($count / $stats['total_companies']) * 100 : 0;
                @endphp
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $status->color }}"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $status->name }}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">{{ $count }}</span>
                            <span class="text-gray-400">({{ round($percentage) }}%)</span>
                        </div>
                    </div>
                    <div class="w-full h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" 
                             style="width: {{ $percentage }}%; background-color: {{ $status->color }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Activity Types -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-white">Etkinlik Tipleri</h3>
                <a href="{{ route('activities.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Tümünü Gör →</a>
            </div>
            <div class="p-6">
                @php
                    $activityTypes = [
                        'search' => ['label' => 'Arama', 'color' => '#3B82F6', 'icon' => '🔍'],
                        'call' => ['label' => 'Telefon', 'color' => '#10B981', 'icon' => '📞'],
                        'meeting' => ['label' => 'Toplantı', 'color' => '#8B5CF6', 'icon' => '🤝'],
                        'proposal' => ['label' => 'Teklif', 'color' => '#F59E0B', 'icon' => '📄'],
                        'email' => ['label' => 'E-posta', 'color' => '#6366F1', 'icon' => '📧'],
                        'note' => ['label' => 'Not', 'color' => '#6B7280', 'icon' => '📝'],
                    ];
                    $totalActivities = array_sum($stats['by_activity_type']);
                @endphp
                
                @foreach($activityTypes as $key => $type)
                @php
                    $count = $stats['by_activity_type'][$key] ?? 0;
                    $percentage = $totalActivities > 0 ? ($count / $totalActivities) * 100 : 0;
                @endphp
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <span class="mr-2">{{ $type['icon'] }}</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type['label'] }}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">{{ $count }}</span>
                        </div>
                    </div>
                    <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" 
                             style="width: {{ $percentage }}%; background-color: {{ $type['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Veri Dışa Aktarma
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Export All Companies -->
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-medium text-gray-800 dark:text-white">Tüm Firmalar</h4>
                            <p class="text-xs text-gray-500">Excel formatında</p>
                        </div>
                    </div>
                    <a href="{{ route('companies.export') }}" 
                       class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                        Excel İndir (.xlsx)
                    </a>
                </div>

                <!-- Export by Status -->
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-medium text-gray-800 dark:text-white">Duruma Göre</h4>
                            <p class="text-xs text-gray-500">Filtreli dışa aktarma</p>
                        </div>
                    </div>
                    <select onchange="if(this.value) window.location.href=this.value" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500 text-sm">
                        <option value="">Durum Seçin...</option>
                        @foreach($statusDistribution as $status)
                        <option value="{{ route('companies.export', ['status' => $status->slug]) }}">
                            {{ $status->name }} ({{ $status->companies_count }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Export CSV -->
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-medium text-gray-800 dark:text-white">CSV Format</h4>
                            <p class="text-xs text-gray-500">İçe aktarma için uygun</p>
                        </div>
                    </div>
                    <a href="{{ route('companies.export', ['format' => 'csv']) }}" 
                       class="block w-full text-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition text-sm">
                        CSV İndir (.csv)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-white">Son Etkinlikler (7 Gün)</h3>
            <span class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                {{ $recentActivities->count() }} etkinlik
            </span>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($recentActivities as $activity)
            <div class="p-4 flex items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="w-10 h-10 rounded-full flex items-center justify-center
                            @if($activity->type === 'call') bg-green-100 dark:bg-green-900/50
                            @elseif($activity->type === 'meeting') bg-purple-100 dark:bg-purple-900/50
                            @elseif($activity->type === 'proposal') bg-amber-100 dark:bg-amber-900/50
                            @elseif($activity->type === 'email') bg-indigo-100 dark:bg-indigo-900/50
                            @else bg-blue-100 dark:bg-blue-900/50 @endif">
                    @if($activity->type === 'call')
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    @elseif($activity->type === 'meeting')
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    @elseif($activity->type === 'proposal')
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    @elseif($activity->type === 'email')
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    @endif
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $activity->title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $activity->company->name ?? 'Bilinmiyor' }} • {{ $activity->typeInfo['label'] }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $activity->created_at->format('d.m.Y') }}</span>
                    <span class="text-xs px-2 py-1 rounded-full ml-2
                                @if($activity->status === 'completed') bg-green-100 text-green-600
                                @else bg-amber-100 text-amber-600 @endif">
                        {{ $activity->status === 'completed' ? 'Tamamlandı' : 'Bekliyor' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                Son 7 günde etkinlik bulunmuyor.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
