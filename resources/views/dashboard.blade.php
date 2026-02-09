@extends('layouts.app')

@section('title', 'Dashboard - GMaps CRM')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($stats['by_status'] as $status)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $status->name }}</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $status->companies_count }}</p>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: {{ $status->color }}20">
                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $status->color }}"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Total Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Toplam Firma</h3>
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-4xl font-bold text-gray-800 dark:text-white">{{ $stats['total_companies'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                {{ $stats['companies_with_coordinates'] }} firma koordinatlı
            </p>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Hızlı İşlemler</h3>
            <div class="space-y-3">
                <a href="{{ route('search') }}" 
                   class="flex items-center space-x-3 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="text-blue-600 font-medium">Yeni Arama Yap</span>
                </a>
                <a href="{{ route('maps.index') }}" 
                   class="flex items-center space-x-3 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span class="text-green-600 font-medium">Rota Planla</span>
                </a>
                <a href="{{ route('companies.export') }}" 
                   class="flex items-center space-x-3 p-3 bg-purple-50 dark:bg-purple-900/30 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-purple-600 font-medium">Excel'e Aktar</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Son Etkinlikler</h3>
            <div class="space-y-3">
                @forelse($stats['recent_activities']->take(5) as $activity)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                @if($activity->type === 'search') bg-blue-100 text-blue-600
                                @elseif($activity->type === 'call') bg-green-100 text-green-600
                                @elseif($activity->type === 'meeting') bg-purple-100 text-purple-600
                                @else bg-gray-100 text-gray-600 @endif">
                        @if($activity->type === 'search')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        @elseif($activity->type === 'call')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                            {{ $activity->title }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $activity->company->name ?? 'Bilinmeyen' }} • {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Henüz etkinlik yok</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Companies -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Son Eklenen Firmalar</h3>
            <a href="{{ route('companies.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Tümünü Gör →
            </a>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($stats['recent_companies'] as $company)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                             style="background-color: {{ $company->status->color ?? '#6B7280' }}">
                            {{ $company->initials }}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white">{{ $company->name }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $company->category ?? 'Kategori yok' }} • {{ $company->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="px-2 py-1 text-xs rounded-full" 
                              style="background-color: {{ $company->status->color ?? '#6B7280' }}20; color: {{ $company->status->color ?? '#6B7280' }}">
                            {{ $company->status->name ?? 'Bilinmiyor' }}
                        </span>
                        @if($company->rating)
                        <span class="flex items-center text-sm text-amber-500">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $company->rating }}
                        </span>
                        @endif
                        <a href="{{ route('companies.show', $company) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Detay
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <p>Henüz firma eklenmemiş.</p>
                <a href="{{ route('search') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                    İlk aramanızı yapın →
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
