<?php

namespace App\Services;

use Illuminate\Support\Collection;

class RouteOptimizationService
{
    /**
     * Nearest Neighbor algoritması ile rota optimizasyonu
     * En kısa yolu bulmak için başlangıç noktasından en yakın noktaya 
     * giderek devam eder
     */
    public function optimize(array $startPoint, Collection $companies): array
    {
        if ($companies->isEmpty()) {
            return [
                'route' => [],
                'total_distance' => 0,
                'start_point' => $startPoint,
            ];
        }

        $route = [];
        $totalDistance = 0;
        $unvisited = $companies->values()->all();
        $currentPoint = $startPoint;

        while (!empty($unvisited)) {
            $nearestIndex = $this->findNearest($currentPoint, $unvisited);
            $nearest = $unvisited[$nearestIndex];
            
            $distance = $this->calculateDistance(
                $currentPoint['lat'], 
                $currentPoint['lng'],
                (float) $nearest->latitude, 
                (float) $nearest->longitude
            );
            
            $route[] = [
                'company' => [
                    'id' => $nearest->id,
                    'name' => $nearest->name,
                    'address' => $nearest->address,
                    'phone' => $nearest->phone,
                    'latitude' => (float) $nearest->latitude,
                    'longitude' => (float) $nearest->longitude,
                    'status' => $nearest->status ? [
                        'name' => $nearest->status->name,
                        'color' => $nearest->status->color,
                    ] : null,
                ],
                'distance_from_previous' => round($distance, 1),
                'order' => count($route) + 1,
            ];
            
            $totalDistance += $distance;
            $currentPoint = [
                'lat' => (float) $nearest->latitude, 
                'lng' => (float) $nearest->longitude
            ];
            
            array_splice($unvisited, $nearestIndex, 1);
        }

        return [
            'route' => $route,
            'total_distance' => round($totalDistance, 1),
            'start_point' => $startPoint,
        ];
    }

    /**
     * En yakın noktayı bul
     */
    protected function findNearest(array $point, array $candidates): int
    {
        $minDistance = PHP_FLOAT_MAX;
        $nearestIndex = 0;

        foreach ($candidates as $index => $candidate) {
            $distance = $this->calculateDistance(
                $point['lat'], 
                $point['lng'],
                (float) $candidate->latitude, 
                (float) $candidate->longitude
            );
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestIndex = $index;
            }
        }

        return $nearestIndex;
    }

    /**
     * Haversine formülü ile iki nokta arasındaki mesafeyi hesapla (km)
     * 
     * @param float $lat1 Başlangıç enlem
     * @param float $lon1 Başlangıç boylam
     * @param float $lat2 Bitiş enlem
     * @param float $lon2 Bitiş boylam
     * @return float Mesafe (km)
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Dünya yarıçapı (km)

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Birden fazla nokta için toplam mesafeyi hesapla
     */
    public function calculateTotalDistance(array $points): float
    {
        if (count($points) < 2) {
            return 0;
        }

        $totalDistance = 0;

        for ($i = 0; $i < count($points) - 1; $i++) {
            $totalDistance += $this->calculateDistance(
                $points[$i]['lat'],
                $points[$i]['lng'],
                $points[$i + 1]['lat'],
                $points[$i + 1]['lng']
            );
        }

        return round($totalDistance, 1);
    }
}
