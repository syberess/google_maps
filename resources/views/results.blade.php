<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arama Sonuçları</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1600px; margin: 0 auto; }
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .header h1 { color: #333; margin-bottom: 15px; font-size: 28px; }
        .search-info { display: flex; gap: 30px; flex-wrap: wrap; margin-bottom: 20px; }
        .info-item { display: flex; align-items: center; gap: 8px; color: #666; }
        .info-item strong { color: #333; }
        .actions { display: flex; gap: 15px; flex-wrap: wrap; }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-secondary { background: #f0f0f0; color: #333; }
        .btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
        .map-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        #map { width: 100%; height: 500px; border-radius: 10px; border: 2px solid #e0e0e0; }
        .results-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .results-count { font-size: 18px; color: #333; font-weight: 600; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .result-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .result-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-color: #667eea; }
        .result-card.active { border-color: #667eea; background: #f0f4ff; }
        .card-header-row { display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px; }
        .card-title { font-size: 18px; font-weight: 700; color: #333; display: flex; align-items: center; flex: 1; }
        .detail-btn {
            padding: 6px 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .detail-btn:hover { background: #5568d3; }
        .card-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            margin-top: 10px;
            padding-top: 0;
            border-top: 2px solid transparent;
        }
        .card-details.active {
            max-height: 500px;
            padding-top: 10px;
            border-top: 2px solid #e0e0e0;
        }
        .marker-number {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            font-size: 14px;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .card-rating { color: #ff9800; margin-bottom: 10px; font-weight: 600; }
        .info-row { display: flex; gap: 10px; margin: 8px 0; font-size: 14px; color: #555; }
        .info-icon { min-width: 20px; }
        .link { color: #667eea; text-decoration: none; }
        .link:hover { text-decoration: underline; }
        .no-results { text-align: center; padding: 60px 20px; color: #666; }
        .no-results-icon { font-size: 64px; margin-bottom: 20px; }
        .result-card.selected { border-color: #10b981 !important; background: #ecfdf5 !important; }
        .result-card .checkbox-container { position: absolute; top: 10px; right: 10px; }
        .result-card { position: relative; }
        .btn-crm { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .btn-crm:disabled { opacity: 0.5; cursor: not-allowed; }
        .select-all-container { display: flex; align-items: center; gap: 10px; margin-right: 20px; }
        .select-all-container input { width: 18px; height: 18px; cursor: pointer; }
        .select-all-container label { cursor: pointer; font-weight: 600; color: #333; }
        @media (max-width: 768px) {
            .results-grid { grid-template-columns: 1fr; }
            .search-info { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body x-data="crmApp()">
    <div class="container">
        <div class="header">
            <h1>🗺️ Arama Sonuçları</h1>
            <div class="search-info">
                <div class="info-item"><span>🔍</span><strong>Arama:</strong><span>{{ $keyword }}</span></div>
                <div class="info-item"><span>📍</span><strong>Konum:</strong><span>{{ $location }}</span></div>
                <div class="info-item"><span>�</span><strong>İstenen Sonuç:</strong><span>{{ $result_count }} firma</span></div>
            </div>
            <div class="actions">
                <a href="{{ route('search') }}" class="btn btn-secondary">⬅️ Yeni Arama</a>
                @if(count($results) > 0)
                    <div class="select-all-container">
                        <input type="checkbox" id="selectAll" @change="toggleAll($event.target.checked)">
                        <label for="selectAll">Tümünü Seç (<span x-text="selectedCount"></span>/{{ count($results) }})</label>
                    </div>
                    <button @click="saveToCRM()" :disabled="selectedCount === 0" class="btn btn-crm">
                        ➕ CRM'e Ekle (<span x-text="selectedCount"></span> firma)
                    </button>
                    <a href="{{ route('company.search.export') }}" class="btn btn-success">📥 Excel'e Aktar ({{ count($results) }} firma)</a>
                @endif
            </div>
        </div>
        
        @if(count($results) > 0)
        <div class="map-container">
            <h2 style="color: #333; margin-bottom: 15px; font-size: 20px;">🗺️ Harita Görünümü</h2>
            <div id="map"></div>
        </div>
        
        <div class="results-container">
            <div class="results-count">📊 Toplam <strong>{{ count($results) }}</strong> firma bulundu</div>
            <div class="results-grid">
                @foreach($results as $index => $result)
                    <div class="result-card" 
                         :class="{ 'selected': selectedResults.includes({{ $index }}) }"
                         data-marker-index="{{ $index }}">
                        <div class="checkbox-container">
                            <input type="checkbox" 
                                   :checked="selectedResults.includes({{ $index }})"
                                   @change="toggleResult({{ $index }})"
                                   style="width: 20px; height: 20px; cursor: pointer;">
                        </div>
                        <div class="card-header-row">
                            <div class="card-title">
                                <span class="marker-number">{{ $index + 1 }}</span>
                                {{ $result['name'] }}
                            </div>
                            <button class="detail-btn" onclick="toggleDetails(event, {{ $index }})">📋 Detay</button>
                        </div>
                        @if($result['rating'])
                            <div class="card-rating">
                                ⭐ {{ $result['rating'] }}
                                @if($result['total_ratings'])
                                    <span style="color: #999;">({{ $result['total_ratings'] }})</span>
                                @endif
                            </div>
                        @endif
                        <div class="info-row"><span class="info-icon">📍</span><span>{{ $result['address'] }}</span></div>
                        
                        <div class="card-details" id="details-{{ $index }}">
                            <div style="background: white; padding: 15px; border-radius: 10px;">
                                <h4 style="color: #667eea; margin-bottom: 10px; font-size: 16px;">📋 Detaylı Bilgiler</h4>
                                
                                <div class="info-row">
                                    <span class="info-icon">🏢</span>
                                    <span><strong>İşletme Adı:</strong> {{ $result['name'] }}</span>
                                </div>
                                
                                @if($result['rating'])
                                    <div class="info-row">
                                        <span class="info-icon">⭐</span>
                                        <span><strong>Puan:</strong> {{ $result['rating'] }}/5 
                                        @if($result['total_ratings'])
                                            ({{ $result['total_ratings'] }} değerlendirme)
                                        @endif
                                        </span>
                                    </div>
                                @endif
                                
                                <div class="info-row">
                                    <span class="info-icon">📍</span>
                                    <span><strong>Adres:</strong> {{ $result['address'] }}</span>
                                </div>
                                
                                @if($result['phone'])
                                    <div class="info-row">
                                        <span class="info-icon">📞</span>
                                        <span><strong>Telefon:</strong> <a href="tel:{{ $result['phone'] }}" class="link">{{ $result['phone'] }}</a></span>
                                    </div>
                                @endif
                                
                                @if($result['website'])
                                    <div class="info-row">
                                        <span class="info-icon">🌐</span>
                                        <span><strong>Website:</strong> <a href="{{ $result['website'] }}" target="_blank" class="link">{{ Str::limit($result['website'], 40) }}</a></span>
                                    </div>
                                @endif
                                
                                @if($result['opening_hours'])
                                    <div class="info-row">
                                        <span class="info-icon">🕐</span>
                                        <span><strong>Durum:</strong> {{ $result['opening_hours'] }}</span>
                                    </div>
                                @endif
                                
                                @if($result['types'])
                                    <div class="info-row">
                                        <span class="info-icon">🏷️</span>
                                        <span><strong>Kategoriler:</strong> {{ $result['types'] }}</span>
                                    </div>
                                @endif
                                
                                @if($result['latitude'] && $result['longitude'])
                                    <div class="info-row">
                                        <span class="info-icon">📌</span>
                                        <span><strong>Koordinatlar:</strong> {{ number_format($result['latitude'], 6) }}, {{ number_format($result['longitude'], 6) }}</span>
                                    </div>
                                @endif
                                
                                <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                                    @if($result['latitude'] && $result['longitude'])
                                        <a href="https://www.google.com/maps?q={{ $result['latitude'] }},{{ $result['longitude'] }}" 
                                           target="_blank" 
                                           class="btn" 
                                           style="background: #667eea; color: white; text-decoration: none; font-size: 13px; padding: 8px 16px;">
                                            🗺️ Google Maps'te Aç
                                        </a>
                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $result['latitude'] }},{{ $result['longitude'] }}" 
                                           target="_blank" 
                                           class="btn" 
                                           style="background: #11998e; color: white; text-decoration: none; font-size: 13px; padding: 8px 16px;">
                                            🧭 Yol Tarifi Al
                                        </a>
                                        <button onclick="showMarker({{ $index }})" 
                                                class="btn" 
                                                style="background: #ff9800; color: white; font-size: 13px; padding: 8px 16px;">
                                            📍 Haritada Göster
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <script>
            let map, markers = [], infoWindow;
            
            function initMap() {
                const results = @json($results);
                if (results.length === 0) return;
                
                let centerLat = 0, centerLng = 0, validCount = 0;
                results.forEach(result => {
                    if (result.latitude && result.longitude) {
                        centerLat += parseFloat(result.latitude);
                        centerLng += parseFloat(result.longitude);
                        validCount++;
                    }
                });
                if (validCount === 0) return;
                centerLat /= validCount;
                centerLng /= validCount;
                
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: centerLat, lng: centerLng },
                    zoom: 13
                });
                infoWindow = new google.maps.InfoWindow();
                
                results.forEach((result, index) => {
                    if (result.latitude && result.longitude) {
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(result.latitude), lng: parseFloat(result.longitude) },
                            map: map,
                            title: result.name,
                            label: { text: String(index + 1), color: 'white', fontWeight: 'bold' }
                        });
                        const content = `<div style="padding:10px;max-width:300px;"><h3 style="margin:0 0 10px 0;">${result.name}</h3>${result.rating ? `<div style="color:#ff9800;">⭐ ${result.rating}</div>` : ''}<p style="margin:8px 0;color:#666;">📍 ${result.address}</p><a href="https://www.google.com/maps/dir/?api=1&destination=${result.latitude},${result.longitude}" target="_blank" style="display:inline-block;margin-top:10px;padding:8px 16px;background:#667eea;color:white;text-decoration:none;border-radius:5px;">🧭 Yol Tarifi</a></div>`;
                        marker.addListener('click', () => { infoWindow.setContent(content); infoWindow.open(map, marker); });
                        markers.push(marker);
                    }
                });
                
                if (markers.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => bounds.extend(marker.getPosition()));
                    map.fitBounds(bounds);
                }
            }
            function showMarker(index) {
                if (markers[index]) {
                    map.setCenter(markers[index].getPosition());
                    map.setZoom(16);
                    google.maps.event.trigger(markers[index], 'click');
                    document.querySelectorAll('.result-card').forEach(card => card.classList.remove('active'));
                    document.querySelector(`[data-marker-index="${index}"]`).classList.add('active');
                    document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            function toggleDetails(event, index) {
                event.stopPropagation();
                const detailsDiv = document.getElementById(`details-${index}`);
                const allDetails = document.querySelectorAll('.card-details');
                
                // Diğer açık detayları kapat
                allDetails.forEach(detail => {
                    if (detail.id !== `details-${index}`) {
                        detail.classList.remove('active');
                    }
                });
                
                // Bu detayı aç/kapat
                detailsDiv.classList.toggle('active');
            }
            
            // Google Maps API'yi yükle ve initMap'i çağır
            window.initMap = initMap;
            
            // DOM yüklendikten sonra Google Maps script'ini ekle
            document.addEventListener('DOMContentLoaded', function() {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap';
                script.async = true;
                script.defer = true;
                script.onerror = function() {
                    console.error('Google Maps yüklenemedi!');
                    document.getElementById('map').innerHTML = '<div style="padding: 40px; text-align: center; color: #999;">⚠️ Harita yüklenemedi. API key kontrol edin.</div>';
                };
                document.head.appendChild(script);
            });
        </script>
        
        <script>
            function crmApp() {
                return {
                    selectedResults: [],
                    results: @json($results),
                    
                    get selectedCount() {
                        return this.selectedResults.length;
                    },
                    
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
        @else
        <div class="results-container">
            <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h2>Sonuç Bulunamadı</h2>
                <p>Arama kriterlerinize uygun firma bulunamadı. Lütfen farklı bir arama deneyin.</p>
            </div>
        </div>
        @endif
    </div>
</body>
</html>
