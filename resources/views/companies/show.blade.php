@extends('layouts.app')

@section('title', $company->name . ' - GMaps CRM')
@section('page-title', 'Firma Detay')

@section('header-actions')
<div class="flex items-center space-x-2">
    <a href="{{ $company->google_maps_url }}" target="_blank" 
       class="flex items-center space-x-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        </svg>
        <span>Google Maps</span>
    </a>
    <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
        ← Geri
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Company Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Company Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                         style="background-color: {{ $company->status->color ?? '#6B7280' }}">
                        {{ $company->initials }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $company->name }}</h2>
                        <div class="flex items-center flex-wrap gap-2 mt-2">
                            <span class="px-3 py-1 rounded-full text-sm" 
                                  style="background-color: {{ $company->status->color ?? '#6B7280' }}20; color: {{ $company->status->color ?? '#6B7280' }}">
                                {{ $company->status->name ?? 'Bilinmiyor' }}
                            </span>
                            @if($company->rating)
                            <span class="flex items-center text-amber-500">
                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $company->rating }} / 5 ({{ $company->review_count }} değerlendirme)
                            </span>
                            @endif
                            @if($company->source === 'google_maps')
                            <span class="text-green-600 text-sm">◆ Google Maps</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @if($company->phone)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Telefon</p>
                        <a href="tel:{{ $company->formatted_phone }}" class="text-gray-800 dark:text-white font-medium hover:text-blue-600">
                            {{ $company->phone }}
                        </a>
                    </div>
                </div>
                @endif

                @if($company->website)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Website</p>
                        <a href="{{ $company->website }}" target="_blank" class="text-gray-800 dark:text-white font-medium hover:text-blue-600 break-all">
                            {{ $company->website }}
                        </a>
                    </div>
                </div>
                @endif

                @if($company->category)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kategori</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $company->category }}</p>
                    </div>
                </div>
                @endif

                @if($company->address)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg md:col-span-2">
                    <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Adres</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $company->address }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Status Update -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <form action="{{ route('companies.update-status', $company) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    @method('PATCH')
                    <label class="text-sm text-gray-600 dark:text-gray-400">Durum:</label>
                    <select name="status_id" onchange="this.form.submit()"
                            class="flex-1 max-w-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500">
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ $company->status_id == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Enriched Data -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Zenginleştirilmiş Veriler
            </h3>
            
            <form action="{{ route('companies.enriched-data', $company) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">E-posta</label>
                        <input type="email" name="email" value="{{ $company->enrichedData->email ?? '' }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="info@example.com">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">İkinci Telefon</label>
                        <input type="text" name="secondary_phone" value="{{ $company->enrichedData->secondary_phone ?? '' }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="+90 xxx xxx xx xx">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Mobil Telefon</label>
                        <input type="text" name="mobile_phone" value="{{ $company->enrichedData->mobile_phone ?? '' }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="+90 5xx xxx xx xx">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">İletişim Kişisi</label>
                        <input type="text" name="contact_person" value="{{ $company->enrichedData->contact_person ?? '' }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="Ad Soyad">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">LinkedIn</label>
                        <input type="url" name="linkedin" value="{{ $company->enrichedData->linkedin ?? '' }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="https://linkedin.com/company/...">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Instagram</label>
                        <input type="text" name="instagram" value="{{ $company->enrichedData->instagram ?? '' }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="@kullanici_adi">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Kaydet
                    </button>
                </div>
            </form>
        </div>

        <!-- Notes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Notlar</h3>
            <form action="{{ route('companies.notes', $company) }}" method="POST">
                @csrf
                @method('PATCH')
                <textarea name="notes" rows="4"
                          class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                          placeholder="Firma hakkında notlarınızı buraya yazın...">{{ $company->notes }}</textarea>
                <div class="mt-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Notu Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column - Activities -->
    <div class="space-y-6">
        <!-- Add Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Etkinlik Ekle</h3>
            <form action="{{ route('activities.store') }}" method="POST">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tip</label>
                        <select name="type" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500">
                            @foreach(\App\Models\Activity::TYPES as $key => $type)
                            <option value="{{ $key }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Başlık</label>
                        <input type="text" name="title" required
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                               placeholder="Etkinlik başlığı">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Açıklama</label>
                        <textarea name="description" rows="2"
                                  class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                                  placeholder="Detaylar..."></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Ekle
                    </button>
                </div>
            </form>
        </div>

        <!-- Activity History -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Etkinlik Geçmişi</h3>
            
            <div class="space-y-4">
                @forelse($company->activities as $activity)
                <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs
                                @if($activity->type === 'search') bg-blue-100 text-blue-600
                                @elseif($activity->type === 'call') bg-green-100 text-green-600
                                @elseif($activity->type === 'meeting') bg-purple-100 text-purple-600
                                @elseif($activity->type === 'proposal') bg-amber-100 text-amber-600
                                @elseif($activity->type === 'email') bg-indigo-100 text-indigo-600
                                @else bg-gray-100 text-gray-600 @endif">
                        @if($activity->type === 'search')
                            🔍
                        @elseif($activity->type === 'call')
                            📞
                        @elseif($activity->type === 'meeting')
                            👥
                        @elseif($activity->type === 'proposal')
                            📄
                        @elseif($activity->type === 'email')
                            ✉️
                        @else
                            📝
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-gray-800 dark:text-white text-sm">{{ $activity->title }}</p>
                            <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                        @if($activity->description)
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $activity->description }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $activity->typeInfo['label'] }}
                            @if($activity->status === 'pending')
                                <span class="text-amber-500">• Beklemede</span>
                            @endif
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 dark:text-gray-400 py-4">Henüz etkinlik yok</p>
                @endforelse
            </div>
        </div>

        <!-- Map Preview -->
        @if($company->latitude && $company->longitude)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Konum</h3>
            <div id="map" class="h-48 rounded-lg"></div>
            <p class="text-xs text-gray-500 mt-2">
                📍 {{ $company->latitude }}, {{ $company->longitude }}
            </p>
        </div>
        @endif
    </div>
</div>

@if($company->latitude && $company->longitude)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map').setView([{{ $company->latitude }}, {{ $company->longitude }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        
        L.marker([{{ $company->latitude }}, {{ $company->longitude }}])
            .addTo(map)
            .bindPopup('{{ $company->name }}')
            .openPopup();
    });
</script>
@endpush
@endif
@endsection
