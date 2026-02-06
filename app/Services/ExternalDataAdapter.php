<?php

namespace App\Services;

class ExternalDataAdapter
{
    /**
     * Harici API'den gelen veriyi projedeki formata çevir
     */
    public static function adaptExternalData($externalData)
    {
        $formatted = [];

        foreach ($externalData as $item) {
            $formatted[] = [
                'name' => $item['name'] ?? 'N/A',
                'address' => $item['address'] ?? 'N/A',
                'phone' => $item['phone_international'] ?? $item['phone'] ?? 'N/A',
                'website' => $item['website'] ?? 'N/A',
                'rating' => $item['rating'] ?? 'N/A',
                'total_ratings' => $item['rating_count'] ?? 'N/A',
                'latitude' => $item['latitude'] ?? 'N/A',
                'longitude' => $item['longitude'] ?? 'N/A',
                'place_id' => $item['google_place_id'] ?? 'N/A',
                'types' => is_array($item['types']) ? implode(', ', $item['types']) : ($item['types'] ?? 'N/A'),
                'opening_hours' => ($item['business_status'] ?? '') === 'OPERATIONAL' ? 'Aktif' : 'N/A',
                'google_maps_url' => $item['google_maps_url'] ?? null,
            ];
        }

        return $formatted;
    }
}
