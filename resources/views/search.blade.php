@extends('layouts.app')

@section('title', 'Google Maps Arama - GMaps CRM')
@section('page-title', 'Google Maps Firma Arama')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="searchApp()">
    <!-- Search Form -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-purple-600 rounded-t-xl">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Firma Arama
                </h2>
                <p class="text-blue-100 text-sm mt-1">Google Maps veritabanından firma arayın</p>
            </div>
            
            <form action="{{ route('company.search') }}" method="POST" class="p-6 space-y-5">
                @csrf
                
                <div>
                    <label for="keyword" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🔍 Arama Kelimesi
                    </label>
                    <input 
                        type="text" 
                        id="keyword" 
                        name="keyword" 
                        placeholder="Örn: Restoran, Kafe, Market"
                        value="{{ old('keyword', session('search_keyword', '')) }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Aramak istediğiniz firma türünü girin</p>
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        📍 Konum
                    </label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        placeholder="Örn: İstanbul, Ankara, İzmir"
                        value="{{ old('location', session('search_location', '')) }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Aramak istediğiniz şehir veya bölge</p>
                </div>
                
                <div>
                    <label for="result_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        📊 Sonuç Sayısı
                    </label>
                    <select 
                        id="result_count" 
                        name="result_count" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                        <option value="10">10 sonuç</option>
                        <option value="20" selected>20 sonuç</option>
                        <option value="30">30 sonuç</option>
                        <option value="40">40 sonuç</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-300 transition shadow-lg">
                    🚀 Aramaya Başla
                </button>
            </form>
            
            <div class="px-6 pb-6">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <strong>İpucu:</strong> Arama sonuçlarından beğendiklerinizi seçip CRM'e ekleyebilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Results Area -->
    <div class="lg:col-span-2">
        @if(isset($results) && count($results) > 0)
        <div class="space-y-4">
            <!-- Results Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAll" @change="toggleAll($event.target.checked)"
                                   class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <label for="selectAll" class="ml-2 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                Tümünü Seç
                            </label>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">
                            {{ count($results) }} sonuç bulundu
                        </span>
                        <span class="text-sm text-gray-500" x-show="selectedResults.length > 0">
                            (<span x-text="selectedResults.length"></span> seçili)
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button @click="saveToCRM()" 
                                :disabled="selectedResults.length === 0"
                                class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            CRM'e Ekle
                        </button>
                        <a href="{{ route('company.search.export') }}" 
                           class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Excel İndir
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Results List -->
            <div class="space-y-3">
                @foreach($results as $index => $result)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow"
                     :class="{ 'ring-2 ring-blue-500': selectedResults.includes({{ $index }}) }">
                    <div class="flex items-start space-x-4">
                        <div class="flex items-center pt-1">
                            <input type="checkbox" 
                                   :checked="selectedResults.includes({{ $index }})"
                                   @change="toggleResult({{ $index }})"
                                   class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white text-lg">
                                        {{ $result['name'] }}
                                    </h3>
                                    @if(isset($result['rating']))
                                    <div class="flex items-center mt-1">
                                        <div class="flex text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round($result['rating']))
                                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $result['rating'] }} ({{ $result['user_ratings_total'] ?? 0 }} yorum)
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                
                                @if(isset($result['business_status']) && $result['business_status'] === 'OPERATIONAL')
                                <span class="px-2 py-1 bg-green-100 text-green-600 text-xs rounded-full">Aktif</span>
                                @endif
                            </div>
                            
                            <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ $result['formatted_address'] ?? $result['vicinity'] ?? 'Adres bilgisi yok' }}
                            </p>
                            
                            <div class="flex items-center flex-wrap gap-2 mt-3">
                                @if(isset($result['types']) && is_array($result['types']))
                                    @foreach(array_slice($result['types'], 0, 3) as $type)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </span>
                                    @endforeach
                                @endif
                                
                                @if(isset($result['phone']))
                                <a href="tel:{{ $result['phone'] }}" class="flex items-center px-2 py-1 bg-green-100 text-green-600 text-xs rounded hover:bg-green-200">
                                    📞 {{ $result['phone'] }}
                                </a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-2">
                            @if(isset($result['geometry']['location']))
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $result['geometry']['location']['lat'] }},{{ $result['geometry']['location']['lng'] }}" 
                               target="_blank"
                               class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Haritada Gör">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @elseif(isset($error))
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
            <svg class="w-12 h-12 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-lg font-semibold text-red-800 dark:text-red-400 mb-2">Hata Oluştu</h3>
            <p class="text-red-600 dark:text-red-300">{{ $error }}</p>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Arama Yapın</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                Soldaki formdan arama kriterlerini girerek Google Maps üzerinden firma arayabilirsiniz. 
                Sonuçları seçip doğrudan CRM'e ekleyebilirsiniz.
            </p>
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('companies.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    → Kayıtlı Firmaları Görüntüle
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function searchApp() {
    return {
        selectedResults: [],
        results: @json($results ?? []),
        
        toggleResult(index) {
            const idx = this.selectedResults.indexOf(index);
            if (idx > -1) {
                this.selectedResults.splice(idx, 1);
            } else {
                this.selectedResults.push(index);
            }
        },
        
        toggleAll(checked) {
            if (checked) {
                this.selectedResults = this.results.map((_, i) => i);
            } else {
                this.selectedResults = [];
            }
        },
        
        async saveToCRM() {
            if (this.selectedResults.length === 0) return;
            
            const selectedData = this.selectedResults.map(i => this.results[i]);
            
            try {
                const response = await fetch('{{ route("companies.bulk-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ companies: selectedData })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(`${data.added} firma CRM'e eklendi! ${data.skipped > 0 ? `(${data.skipped} firma zaten kayıtlı)` : ''}`);
                    window.location.href = '{{ route("companies.index") }}';
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            } catch (error) {
                alert('Bir hata oluştu: ' + error.message);
            }
        }
    }
}
</script>
@endpush
@endsection
