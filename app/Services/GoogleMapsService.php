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
     * Google Places API ile firma arama
     */
    public function searchCompanies($keyword, $location, $radius)
    {
        // Önce konumun koordinatlarını al
        $coordinates = $this->getCoordinates($location);
        
        if (!$coordinates) {
            throw new \Exception("Konum bulunamadı: {$location}");
        }

        // Places API ile arama yap
        $results = $this->searchNearbyPlaces(
            $keyword,
            $coordinates['lat'],
            $coordinates['lng'],
            $radius
        );

        return $results;
    }

    /**
     * Geocoding API ile konum adresini koordinata çevir
     */
    protected function getCoordinates($location)
    {
        $response = Http::get("{$this->baseUrl}/geocode/json", [
            'address' => $location,
            'key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['results'][0]['geometry']['location'])) {
                return [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                ];
            }
        }

        return null;
    }

    /**
     * Places API ile yakındaki yerleri ara
     */
    protected function searchNearbyPlaces($keyword, $lat, $lng, $radius)
    {
        $allResults = [];
        $nextPageToken = null;

        do {
            $params = [
                'location' => "{$lat},{$lng}",
                'radius' => $radius,
                'keyword' => $keyword,
                'key' => $this->apiKey,
                'language' => 'tr',
            ];

            if ($nextPageToken) {
                $params = [
                    'pagetoken' => $nextPageToken,
                    'key' => $this->apiKey,
                ];
                // Page token için kısa bir bekleme gerekli
                sleep(2);
            }

            $response = Http::get("{$this->baseUrl}/place/nearbysearch/json", $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['results'])) {
                    foreach ($data['results'] as $place) {
                        $placeDetails = $this->getPlaceDetails($place['place_id']);
                        $allResults[] = array_merge($place, $placeDetails);
                    }
                }

                $nextPageToken = $data['next_page_token'] ?? null;
            } else {
                break;
            }

        } while ($nextPageToken && count($allResults) < 60); // Maksimum 60 sonuç (3 sayfa x 20)

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
        $formatted = [];

        foreach ($results as $result) {
            $formatted[] = [
                'name' => $result['name'] ?? 'N/A',
                'address' => $result['vicinity'] ?? 'N/A',
                'phone' => $result['phone'] ?? 'N/A',
                'website' => $result['website'] ?? 'N/A',
                'rating' => $result['rating_score'] ?? 'N/A',
                'total_ratings' => $result['total_ratings'] ?? 'N/A',
                'latitude' => $result['geometry']['location']['lat'] ?? 'N/A',
                'longitude' => $result['geometry']['location']['lng'] ?? 'N/A',
                'place_id' => $result['place_id'] ?? 'N/A',
                'types' => implode(', ', $result['types'] ?? []),
                'opening_hours' => !empty($result['opening_hours']) ? implode(' | ', $result['opening_hours']) : 'N/A',
            ];
        }

        return $formatted;
    }
}
