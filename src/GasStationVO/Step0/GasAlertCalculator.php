<?php

declare(strict_types=1);

namespace App\GasStationVO\Step0;

class GasAlertCalculator
{
    /** @var array{lat: float, long: float}[] */
    private readonly array $gasStationsLocations;

    public function __construct(array $gasStationsLocations)
    {
        $this->gasStationsLocations = $gasStationsLocations;
    }

    public function calculate(float $lat, float $long, float $fuelLeft, float $averageFuelConsumptionPer100km): int
    {
        if ($lat < -90 || $lat > 90) {
            throw new \InvalidArgumentException();
        }
        if ($long < -180 || $long > 180) {
            throw new \InvalidArgumentException();
        }
        $distanceLeftInKM = $fuelLeft*100/$averageFuelConsumptionPer100km;
        $minDistanceToStation = PHP_FLOAT_MAX;
        foreach ($this->gasStationsLocations as $gasStationLatLong) {
            $x = deg2rad($gasStationLatLong['long']-$long) * cos(deg2rad($lat+$gasStationLatLong['lat'])/2);
            $y = deg2rad($lat-$gasStationLatLong['lat']);
            $R = 6372.8;
            $d = sqrt($x*$x + $y*$y) * $R;

            if ($minDistanceToStation > $d) {
                $minDistanceToStation = $d;
            }
        }
        $zapas = $distanceLeftInKM - $minDistanceToStation;
        if ($zapas < 2) {
            return 5;
        }
        if ($zapas >= 2 && $zapas <= 4) {
            return 3;
        }
        if ($zapas > 4) {
            return 0;
        }
        throw new \DomainException();
    }
}
