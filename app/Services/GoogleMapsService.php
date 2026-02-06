<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleMapsService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    /**
     * Google Places API ile firma arama (Text Search kullanarak)
     */
    public function searchCompanies($keyword, $location, $resultCount)
    {
        \Log::info('searchCompanies çağrıldı', [
            'keyword' => $keyword,
            'location' => $location,
            'result_count' => $resultCount,
            'api_key' => substr($this->apiKey, 0, 20) . '...'
        ]);
        
        // Text Search ile doğrudan arama yap
        \Log::info('Text Search API araması başlıyor...');
        $results = $this->textSearch($keyword, $location, $resultCount);
        
        \Log::info('Text Search API sonuç sayısı: ' . count($results));

        return $results;
    }

    /**
     * Location string'ine göre dinamik yarıçap belirle
     */
    protected function determineRadius($location)
    {
        // Küçük harfe çevir
        $location = strtolower(trim($location));
        
        // Virgül veya boşlukla ayrılmış birden fazla kelime var mı?
        $hasComma = strpos($location, ',') !== false;
        $wordCount = count(array_filter(explode(' ', $location)));
        
        // Spesifik semt/ilçe belirtilmişse → 5km
        if ($hasComma || $wordCount > 1) {
            \Log::info('Spesifik konum tespit edildi → 5km yarıçap');
            return 5000; // 5km
        }
        
        // Sadece şehir adı → 20km
        \Log::info('Genel konum tespit edildi → 20km yarıçap');
        return 20000; // 20km
    }

    /**
     * Places Text Search API ile arama
     */
    protected function textSearch($keyword, $location, $maxResults = 20)
    {
        $allResults = [];
        $nextPageToken = null;
        $pageCount = 0;

        // Query oluştur: "keyword in location" formatı
        $query = "{$keyword} in {$location}";
        \Log::info('Text Search query:', ['query' => $query]);

        // Location bias için koordinat al
        $coordinates = $this->getCoordinates($location);
        $locationBias = null;
        
        if ($coordinates) {
            // 50km yarıçaplı bir bias oluştur (sonuçları bu bölgeye önceliklendirir)
            $locationBias = "circle:50000@{$coordinates['lat']},{$coordinates['lng']}";
            \Log::info('Location bias eklendi:', ['bias' => $locationBias]);
        } else {
            \Log::warning('Koordinat bulunamadı, location bias olmadan devam ediliyor');
        }

        do {
            $pageCount++;
            \Log::info("Text Search API Sayfa {$pageCount} çağrılıyor...");
            
            $params = [
                'query' => $query,
                'key' => $this->apiKey,
                'language' => 'tr',
            ];
            
            // Location bias varsa ekle
            if ($locationBias) {
                $params['locationbias'] = $locationBias;
            }

            if ($nextPageToken) {
                \Log::info('Next page token ile devam ediliyor...');
                $params = [
                    'pagetoken' => $nextPageToken,
                    'key' => $this->apiKey,
                ];
                // Page token için kısa bir bekleme gerekli
                sleep(2);
            }
            
            \Log::info('Text Search API parametreleri:', array_merge($params, ['key' => substr($params['key'], 0, 20) . '...']));

            $response = Http::get("{$this->baseUrl}/place/textsearch/json", $params);
            
            \Log::info('Text Search API yanıt durumu:', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Text Search API yanıt:', [
                    'status' => $data['status'] ?? 'UNKNOWN',
                    'results_count' => count($data['results'] ?? []),
                    'has_next_page' => isset($data['next_page_token'])
                ]);
                
                if (isset($data['status']) && $data['status'] !== 'OK' && $data['status'] !== 'ZERO_RESULTS') {
                    \Log::error('Text Search API hata durumu:', [
                        'status' => $data['status'],
                        'error_message' => $data['error_message'] ?? 'N/A'
                    ]);
                }
                
                if (isset($data['results'])) {
                    \Log::info("Sayfa {$pageCount}: " . count($data['results']) . ' sonuç bulundu');
                    
                    foreach ($data['results'] as $index => $place) {
                        // Maksimum sonuç sayısına ulaştık mı?
                        if (count($allResults) >= $maxResults) {
                            \Log::info("Maksimum sonuç sayısına ({$maxResults}) ulaşıldı, durduruluyor.");
                            break 2; // İki döngüden de çık
                        }
                        
                        \Log::info("Place {$index}: " . ($place['name'] ?? 'N/A') . ' - ' . ($place['formatted_address'] ?? 'N/A'));
                        
                        // Her 5 istekte 1 saniye bekle (rate limit için)
                        if (count($allResults) % 5 === 0 && count($allResults) > 0) {
                            usleep(200000); // 0.2 saniye
                        }
                        
                        $placeDetails = $this->getPlaceDetails($place['place_id']);
                        $allResults[] = array_merge($place, $placeDetails);
                    }
                    
                    \Log::info('Toplam sonuç sayısı: ' . count($allResults));
                } else {
                    \Log::warning('Text Search API yanıtında results array yok');
                }

                $nextPageToken = $data['next_page_token'] ?? null;
                
                // Maksimum sonuç sayısına ulaştıysak pagination'ı sonlandır
                if (count($allResults) >= $maxResults) {
                    \Log::info("Maksimum sonuç sayısına ulaşıldı, pagination sonlandırılıyor.");
                    $nextPageToken = null;
                }
                
                \Log::info('Next page token:', ['token' => $nextPageToken ? 'VAR' : 'YOK']);
            } else {
                \Log::error('Text Search API başarısız:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                break;
            }

        } while ($nextPageToken && count($allResults) < $maxResults && $pageCount < 3); // Maksimum 3 sayfa (60 sonuç)

        \Log::info('Text Search tamamlandı. Toplam sonuç: ' . count($allResults));
        
        return $this->formatResults($allResults);
    }


    /**
     * Geocoding API ile konum adresini koordinata çevir
     */
    protected function getCoordinates($location)
    {
        $url = "{$this->baseUrl}/geocode/json";
        \Log::info('Geocoding API çağrısı:', ['url' => $url, 'location' => $location]);
        
        $response = Http::get($url, [
            'address' => $location,
            'key' => $this->apiKey,
        ]);
        
        \Log::info('Geocoding API yanıtı:', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['results'][0]['geometry']['location'])) {
                $coords = [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                ];
                \Log::info('Koordinatlar parse edildi:', $coords);
                return $coords;
            } else {
                \Log::warning('Geocoding yanıtında results bulunamadı');
            }
        } else {
            \Log::error('Geocoding API başarısız:', ['status' => $response->status()]);
        }

        return null;
    }

    /**
     * Places API ile yakındaki yerleri ara
     */
    protected function searchNearbyPlaces($keyword, $lat, $lng, $radius, $maxResults = 20)
    {
        $allResults = [];
        $nextPageToken = null;
        $pageCount = 0;

        do {
            $pageCount++;
            \Log::info("Places API Sayfa {$pageCount} çağrılıyor...");
            
            $params = [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'keyword' => $keyword,
                'key' => $this->apiKey,
                'language' => 'tr',
            ];

            if ($nextPageToken) {
                \Log::info('Next page token ile devam ediliyor...');
                $params = [
                    'pagetoken' => $nextPageToken,
                    'key' => $this->apiKey,
                ];
                // Page token için kısa bir bekleme gerekli
                sleep(2);
            }
            
            \Log::info('Places API parametreleri:', array_merge($params, ['key' => substr($params['key'], 0, 20) . '...']));

            $response = Http::get("{$this->baseUrl}/place/nearbysearch/json", $params);
            
            \Log::info('Places API yanıt durumu:', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Places API yanıt:', [
                    'status' => $data['status'] ?? 'UNKNOWN',
                    'results_count' => count($data['results'] ?? []),
                    'has_next_page' => isset($data['next_page_token'])
                ]);
                
                if (isset($data['status']) && $data['status'] !== 'OK' && $data['status'] !== 'ZERO_RESULTS') {
                    \Log::error('Places API hata durumu:', [
                        'status' => $data['status'],
                        'error_message' => $data['error_message'] ?? 'N/A'
                    ]);
                }
                
                if (isset($data['results'])) {
                    \Log::info("Sayfa {$pageCount}: " . count($data['results']) . ' sonuç bulundu');
                    
                    foreach ($data['results'] as $index => $place) {
                        \Log::info("Place {$index}: " . ($place['name'] ?? 'N/A') . ' - Details alınıyor...');
                        
                        // Her 5 istekte 1 saniye bekle (rate limit için)
                        if (count($allResults) % 5 === 0 && count($allResults) > 0) {
                            usleep(200000); // 0.2 saniye
                        }
                        
                        $placeDetails = $this->getPlaceDetails($place['place_id']);
                        $allResults[] = array_merge($place, $placeDetails);
                    }
                    
                    \Log::info('Toplam sonuç sayısı: ' . count($allResults));
                } else {
                    \Log::warning('Places API yanıtında results array yok');
                }

                $nextPageToken = $data['next_page_token'] ?? null;
                \Log::info('Next page token:', ['token' => $nextPageToken ? 'VAR' : 'YOK']);
            } else {
                \Log::error('Places API başarısız:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                break;
            }

        } while ($nextPageToken && count($allResults) < $maxResults && $pageCount < 3); // İstenen sonuç sayısına kadar
        
        \Log::info('searchNearbyPlaces tamamlandı. Toplam: ' . count($allResults) . ' sonuç');

        return $this->formatResults($allResults);
    }

    /**
     * Place Details API ile detaylı bilgi al
     */
    protected function getPlaceDetails($placeId)
    {
        $response = Http::get("{$this->baseUrl}/place/details/json", [
            'place_id' => $placeId,
            'fields' => 'formatted_phone_number,website,opening_hours,rating,user_ratings_total,reviews',
            'key' => $this->apiKey,
            'language' => 'tr',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['result'])) {
                return [
                    'phone' => $data['result']['formatted_phone_number'] ?? null,
                    'website' => $data['result']['website'] ?? null,
                    'opening_hours' => $data['result']['opening_hours']['weekday_text'] ?? [],
                    'rating_score' => $data['result']['rating'] ?? null,
                    'total_ratings' => $data['result']['user_ratings_total'] ?? null,
                ];
            }
        }

        return [];
    }

    /**
     * Sonuçları formatla
     */
    protected function formatResults($results)
    {
        \Log::info('Sonuçlar formatlanıyor... Ham sonuç sayısı: ' . count($results));
        
        $formatted = [];

        foreach ($results as $index => $result) {
            $formatted[] = [
                'name' => $result['name'] ?? '',
                'address' => $result['vicinity'] ?? $result['formatted_address'] ?? '',
                'phone' => $result['phone'] ?? '',
                'website' => $result['website'] ?? '',
                'rating' => $result['rating_score'] ?? $result['rating'] ?? '',
                'total_ratings' => $result['total_ratings'] ?? $result['user_ratings_total'] ?? '',
                'latitude' => $result['geometry']['location']['lat'] ?? '',
                'longitude' => $result['geometry']['location']['lng'] ?? '',
                'place_id' => $result['place_id'] ?? '',
                'types' => implode(', ', $result['types'] ?? []),
                'opening_hours' => !empty($result['opening_hours']) 
                    ? (is_array($result['opening_hours']) ? implode(' | ', $result['opening_hours']) : $result['opening_hours'])
                    : (isset($result['opening_hours']['open_now']) 
                        ? ($result['opening_hours']['open_now'] ? 'Açık' : 'Kapalı') 
                        : ''),
            ];
            
            if ($index < 3) {
                \Log::info("Formatlanmış sonuç {$index}:", $formatted[$index]);
            }
        }
        
        \Log::info('Formatlama tamamlandı. Toplam: ' . count($formatted));

        return $formatted;
    }
}
