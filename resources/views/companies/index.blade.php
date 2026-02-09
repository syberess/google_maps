@extends('layouts.app')

@section('title', 'Firmalar - GMaps CRM')
@section('page-title', 'Firma Yönetimi')

@section('header-actions')
<div class="flex items-center space-x-2">
    <a href="{{ route('companies.export') }}" class="flex items-center space-x-1 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-700 dark:text-gray-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>XLSX</span>
    </a>
    <a href="{{ route('companies.export', ['format' => 'csv']) }}" class="flex items-center space-x-1 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-700 dark:text-gray-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>CSV</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Status Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($statusCounts as $status)
        <a href="{{ route('companies.index', ['status' => $status->slug]) }}" 
           class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition
                  {{ request('status') === $status->slug ? 'ring-2 ring-blue-500' : '' }}">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full" style="background-color: {{ $status->color }}"></div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $status->companies_count }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $status->name }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form action="{{ route('companies.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="İsim, kategori veya telefon ara..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:text-white">
                </div>
            </div>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:text-white">
                <option value="">Tüm Durumlar</option>
                @foreach($statuses as $status)
                <option value="{{ $status->slug }}" {{ request('status') === $status->slug ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
                @endforeach
            </select>

            <!-- Category Filter -->
            <select name="category" class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:text-white">
                <option value="">Tüm Kategoriler</option>
                @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Filtrele
            </button>

            @if(request()->hasAny(['search', 'status', 'category']))
            <a href="{{ route('companies.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white">
                Temizle
            </a>
            @endif
        </form>
    </div>

    <!-- Companies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($companies as $company)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition">
            <!-- Header -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                         style="background-color: {{ $company->status->color ?? '#6B7280' }}">
                        {{ $company->initials }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-gray-800 dark:text-white truncate" title="{{ $company->name }}">
                            {{ Str::limit($company->name, 25) }}
                        </h3>
                        <div class="flex items-center flex-wrap gap-1 text-xs">
                            <span class="px-2 py-0.5 rounded-full" 
                                  style="background-color: {{ $company->status->color ?? '#6B7280' }}20; color: {{ $company->status->color ?? '#6B7280' }}">
                                {{ $company->status->name ?? 'Bilinmiyor' }}
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
                            <span class="text-green-500">✓ Zengin</span>
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
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                        <a href="{{ route('companies.show', $company) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Detay Görüntüle
                        </a>
                        <a href="{{ $company->google_maps_url }}" target="_blank"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Google Maps'te Aç
                        </a>
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" 
                              onsubmit="return confirm('Bu firmayı silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                Sil
                            </button>
                        </form>
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
                <a href="tel:{{ $company->formatted_phone }}" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600">
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
                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span class="text-xs line-clamp-2">{{ $company->address }}</span>
                </div>
                @endif
            </div>

            <!-- Enriched Data Section -->
            @if($company->hasEnrichedData())
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700" x-data="{ open: false }">
                <button @click="open = !open" class="text-xs text-purple-600 hover:text-purple-800 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Zenginleştirilmiş Veriler</span>
                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-400">
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
                    <span>⏱️ Son Etkinlik</span>
                    <span>{{ $company->activities->first()->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">
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
                            class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500">
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ $company->status_id == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                        @endforeach
                    </select>
                    
                    <a href="{{ route('companies.show', $company) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Detay">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Henüz firma bulunamadı.</p>
            <a href="{{ route('search') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Arama Yap
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($companies->hasPages())
    <div class="mt-6">
        {{ $companies->links() }}
    </div>
    @endif
</div>
@endsection
