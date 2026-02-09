@extends('layouts.app')

@section('title', 'Harita & Rota - GMaps CRM')
@section('page-title', 'Harita & Rota Planlama')

@section('header-actions')
<span class="text-sm text-gray-500 dark:text-gray-400">
    {{ $companies->count() }} Adresi Firma
</span>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="mapApp()">
    <!-- Left Panel -->
    <div class="space-y-6">
        <!-- Company Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Firma Seçimi
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Rotaya eklemek istediğiniz firmaları seçin</p>
                </div>
                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded-full" x-text="selectedCompanies.length + ' seçili'">0 seçili</span>
            </div>
            
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex space-x-2">
                <button @click="selectAll()" class="flex-1 px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                    Tümünü Seç
                </button>
                <button @click="clearSelection()" class="flex-1 px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                    Temizle
                </button>
            </div>
            
            <div class="max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($companies as $company)
                <label class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                    <input type="checkbox" 
                           :checked="selectedCompanies.includes({{ $company->id }})"
                           @change="toggleCompany({{ $company->id }})"
                           class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="font-medium text-gray-800 dark:text-white text-sm truncate">{{ $company->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $company->address }}</p>
                    </div>
                    <span class="px-2 py-0.5 text-xs rounded-full" 
                          style="background-color: {{ $company->status->color ?? '#6B7280' }}20; color: {{ $company->status->color ?? '#6B7280' }}">
                        {{ $company->status->name ?? '' }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Route Calculation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 dark:text-white">Rota Hesapla</h3>
                <span class="text-xs text-gray-500" x-show="currentLocation">
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                    Konumunuz Aktif
                </span>
            </div>
            
            <button @click="getCurrentLocation()" 
                    class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition mb-3"
                    :disabled="gettingLocation">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-text="gettingLocation ? 'Konum alınıyor...' : 'Konumumu Al'"></span>
            </button>
            
            <button @click="calculateRoute()" 
                    class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="selectedCompanies.length < 1 || !currentLocation || calculating">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span x-text="calculating ? 'Hesaplanıyor...' : 'Rota Hesapla (' + selectedCompanies.length + ' nokta)'"></span>
            </button>
            
            <p class="text-xs text-gray-500 mt-2 text-center" x-show="selectedCompanies.length < 1">
                En az 1 firma seçin
            </p>
        </div>
    </div>

    <!-- Map & Route -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Map -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Harita & Rota
                </h3>
            </div>
            <div id="map" class="h-[500px] rounded-b-xl"></div>
        </div>

        <!-- Route Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700" x-show="routeData" x-cloak>
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Optimum Rota
                    <span class="ml-2 text-sm text-gray-500 font-normal">Konumunuzdan başlayarak en kısa mesafe ile hesaplandı</span>
                </h3>
                <span class="px-3 py-1 text-sm bg-green-100 text-green-600 rounded-full" x-text="'Toplam: ' + routeData?.total_distance + ' km'"></span>
            </div>
            
            <div class="p-4">
                <!-- Start Point -->
                <div class="flex items-center space-x-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg mb-4">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                        📍
                    </div>
                    <div>
                        <p class="font-medium text-green-800 dark:text-green-400">Başlangıç: Mevcut Konumunuz</p>
                        <p class="text-sm text-green-600 dark:text-green-500" x-text="currentLocation?.lat?.toFixed(4) + ', ' + currentLocation?.lng?.toFixed(4)"></p>
                    </div>
                </div>
                
                <!-- Route Steps -->
                <template x-for="(step, index) in routeData?.route" :key="index">
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="relative">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold" x-text="index + 1"></div>
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-full text-xs text-gray-500 whitespace-nowrap" x-text="'↓ ' + step.distance_from_previous + ' km'"></div>
                        </div>
                        <div class="flex-1 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-800 dark:text-white" x-text="step.company.name"></p>
                                <div class="flex space-x-2">
                                    <a :href="'tel:' + step.company.phone" class="p-1.5 bg-green-100 text-green-600 rounded hover:bg-green-200" x-show="step.company.phone">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </a>
                                    <a :href="'https://www.google.com/maps/dir/?api=1&destination=' + step.company.latitude + ',' + step.company.longitude" 
                                       target="_blank"
                                       class="p-1.5 bg-blue-100 text-blue-600 rounded hover:bg-blue-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="step.company.address"></p>
                        </div>
                    </div>
                </template>
                
                <!-- Open in Google Maps -->
                <button @click="openInGoogleMaps()" 
                        class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition mt-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span>Google Maps'te Navigasyonu Başlat</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mapApp() {
    return {
        map: null,
        markers: [],
        routeLine: null,
        selectedCompanies: [],
        currentLocation: null,
        currentLocationMarker: null,
        gettingLocation: false,
        calculating: false,
        routeData: null,
        companies: @json($companies),
        
        init() {
            this.initMap();
            this.addMarkers();
        },
        
        initMap() {
            this.map = L.map('map').setView([39.0, 35.0], 6);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
            
            // Haritaya tıklayarak başlangıç konumu seç
            this.map.on('click', (e) => {
                this.setStartLocation(e.latlng.lat, e.latlng.lng, 'Manuel seçim');
            });
        },
        
        setStartLocation(lat, lng, source = '') {
            this.currentLocation = { lat, lng };
            
            // Önceki marker'ı kaldır
            if (this.currentLocationMarker) {
                this.map.removeLayer(this.currentLocationMarker);
            }
            
            // Yeni marker ekle
            this.currentLocationMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'current-location-marker',
                    html: '<div style="width:30px;height:30px;background:#10b981;border:3px solid white;border-radius:50%;box-shadow:0 2px 10px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;font-size:14px;">🚀</div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                }),
                draggable: true // Sürüklenebilir
            }).addTo(this.map);
            
            this.currentLocationMarker.bindPopup(`<b>Başlangıç Noktası</b><br>${source}<br><small>${lat.toFixed(5)}, ${lng.toFixed(5)}</small>`).openPopup();
            
            // Marker sürüklendiğinde konumu güncelle
            this.currentLocationMarker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.currentLocation = { lat: pos.lat, lng: pos.lng };
                this.currentLocationMarker.setPopupContent(`<b>Başlangıç Noktası</b><br>Manuel seçim<br><small>${pos.lat.toFixed(5)}, ${pos.lng.toFixed(5)}</small>`);
            });
        },
        
        addMarkers() {
            this.companies.forEach(company => {
                const marker = L.marker([company.latitude, company.longitude])
                    .addTo(this.map)
                    .bindPopup(`
                        <div class="p-2">
                            <h3 class="font-bold text-sm">${company.name}</h3>
                            ${company.phone ? `<p class="text-xs">📞 ${company.phone}</p>` : ''}
                            <p class="text-xs text-gray-500">${company.address || ''}</p>
                        </div>
                    `);
                this.markers.push({ id: company.id, marker });
            });
            
            if (this.markers.length > 0) {
                const group = L.featureGroup(this.markers.map(m => m.marker));
                this.map.fitBounds(group.getBounds().pad(0.1));
            }
        },
        
        toggleCompany(id) {
            const index = this.selectedCompanies.indexOf(id);
            if (index > -1) {
                this.selectedCompanies.splice(index, 1);
            } else {
                this.selectedCompanies.push(id);
            }
        },
        
        selectAll() {
            this.selectedCompanies = this.companies.map(c => c.id);
        },
        
        clearSelection() {
            this.selectedCompanies = [];
            this.routeData = null;
            if (this.routeLine) {
                this.map.removeLayer(this.routeLine);
            }
        },
        
        getCurrentLocation() {
            this.gettingLocation = true;
            
            if (!navigator.geolocation) {
                alert('Tarayıcınız konum özelliğini desteklemiyor. Haritaya tıklayarak manuel konum seçebilirsiniz.');
                this.gettingLocation = false;
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.setStartLocation(
                        position.coords.latitude,
                        position.coords.longitude,
                        'GPS Konumunuz'
                    );
                    this.map.setView([position.coords.latitude, position.coords.longitude], 14);
                    this.gettingLocation = false;
                },
                (error) => {
                    alert('Konum alınamadı: ' + error.message + '\n\nHaritaya tıklayarak manuel konum seçebilirsiniz.');
                    this.gettingLocation = false;
                }
            );
        },
        
        async calculateRoute() {
            if (this.selectedCompanies.length < 1) {
                alert('En az 1 firma seçmelisiniz!');
                return;
            }
            if (!this.currentLocation) {
                alert('Önce "Konumumu Al" butonuna tıklayın!');
                return;
            }
            
            this.calculating = true;
            
            try {
                console.log('Rota hesaplanıyor...', {
                    company_ids: this.selectedCompanies,
                    start_lat: this.currentLocation.lat,
                    start_lng: this.currentLocation.lng
                });
                
                const response = await fetch('/api/route/optimize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        company_ids: this.selectedCompanies,
                        start_lat: this.currentLocation.lat,
                        start_lng: this.currentLocation.lng
                    })
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Hatası:', errorText);
                    alert('API Hatası (' + response.status + '): ' + errorText.substring(0, 200));
                    this.calculating = false;
                    return;
                }
                
                const data = await response.json();
                console.log('API Yanıtı:', data);
                
                if (data.success) {
                    this.routeData = data;
                    this.drawRoute(data);
                } else {
                    alert('Rota hesaplanamadı: ' + (data.message || JSON.stringify(data.errors) || 'Bilinmeyen hata'));
                }
            } catch (error) {
                console.error('Fetch hatası:', error);
                alert('Bir hata oluştu: ' + error.message);
            }
            
            this.calculating = false;
        },
        
        drawRoute(data) {
            // Remove old route
            if (this.routeLine) {
                this.map.removeLayer(this.routeLine);
            }
            
            // Create route points
            const points = [
                [data.start_point.lat, data.start_point.lng],
                ...data.route.map(step => [step.company.latitude, step.company.longitude])
            ];
            
            // Draw route line
            this.routeLine = L.polyline(points, {
                color: '#3B82F6',
                weight: 4,
                opacity: 0.8,
                dashArray: '10, 10'
            }).addTo(this.map);
            
            // Add numbered markers
            data.route.forEach((step, index) => {
                L.marker([step.company.latitude, step.company.longitude], {
                    icon: L.divIcon({
                        className: 'route-marker',
                        html: `<div class="w-8 h-8 bg-blue-600 border-2 border-white rounded-full shadow-lg flex items-center justify-center text-white font-bold text-sm">${index + 1}</div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    })
                }).addTo(this.map);
            });
            
            // Fit bounds
            this.map.fitBounds(this.routeLine.getBounds().pad(0.1));
        },
        
        openInGoogleMaps() {
            if (!this.routeData) return;
            
            const route = this.routeData.route;
            const destination = route[route.length - 1];
            const waypoints = route.slice(0, -1).map(s => `${s.company.latitude},${s.company.longitude}`).join('|');
            
            let url = `https://www.google.com/maps/dir/?api=1&origin=${this.currentLocation.lat},${this.currentLocation.lng}&destination=${destination.company.latitude},${destination.company.longitude}`;
            
            if (waypoints) {
                url += `&waypoints=${encodeURIComponent(waypoints)}`;
            }
            
            window.open(url, '_blank');
        }
    }
}
</script>

<style>
    .current-location-marker,
    .route-marker {
        background: transparent !important;
        border: none !important;
    }
</style>
@endpush
@endsection
