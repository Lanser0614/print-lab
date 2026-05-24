<?php

namespace App\Support\Geo;

final class TashkentDeliveryArea
{
    /**
     * Conservative service polygon around Tashkent city.
     *
     * @var list<array{lat: float, lng: float}>
     */
    private const POLYGON = [
        ['lat' => 41.3970, 'lng' => 69.1300],
        ['lat' => 41.4210, 'lng' => 69.2450],
        ['lat' => 41.3950, 'lng' => 69.3700],
        ['lat' => 41.3350, 'lng' => 69.4050],
        ['lat' => 41.2450, 'lng' => 69.3600],
        ['lat' => 41.1900, 'lng' => 69.2550],
        ['lat' => 41.2200, 'lng' => 69.1450],
        ['lat' => 41.3000, 'lng' => 69.1050],
    ];

    public static function contains(float $lat, float $lng): bool
    {
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return false;
        }

        $inside = false;
        $vertices = self::POLYGON;
        $count = count($vertices);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $yi = $vertices[$i]['lat'];
            $xi = $vertices[$i]['lng'];
            $yj = $vertices[$j]['lat'];
            $xj = $vertices[$j]['lng'];

            if (self::isOnSegment($lat, $lng, $yi, $xi, $yj, $xj)) {
                return true;
            }

            $intersects = (($yi > $lat) !== ($yj > $lat))
                && ($lng < (($xj - $xi) * ($lat - $yi) / ($yj - $yi)) + $xi);

            if ($intersects) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    public static function normalizeCity(?string $city): ?string
    {
        if ($city === null) {
            return null;
        }

        $normalized = mb_strtolower(trim($city));

        return match ($normalized) {
            'tashkent', 'toshkent', 'ташкент' => 'Tashkent',
            default => null,
        };
    }

    private static function isOnSegment(float $lat, float $lng, float $latA, float $lngA, float $latB, float $lngB): bool
    {
        $cross = (($lng - $lngA) * ($latB - $latA)) - (($lat - $latA) * ($lngB - $lngA));

        if (abs($cross) > 0.0000001) {
            return false;
        }

        return $lng >= min($lngA, $lngB) - 0.0000001
            && $lng <= max($lngA, $lngB) + 0.0000001
            && $lat >= min($latA, $latB) - 0.0000001
            && $lat <= max($latA, $latB) + 0.0000001;
    }
}
