<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;

class Car
{
    public const CAR_SIZE_SMALL = 'small';
    public const CAR_SIZE_LARGE = 'large';

    protected float $profit;
    protected float $fuelAdded;

    public function __construct(protected string $licencePlate, protected string $size, protected CarFuel $fuel)
    {
    }

    public function getLicencePlate(): string
    {
        return $this->licencePlate;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function getFuel(): CarFuel
    {
        return $this->fuel;
    }

    public function getProfit(): float
    {
        $this->profit = $this->profit ?? $this->calculateProfit();
        return $this->profit;
    }

    protected function calculateProfit(): float
    {
        $parkingFee = Config::get('car.parking_fee.' . $this->size);

        $refuelRate = Config::get('car.refuel_fee');
        $refuelFee = $this->getFuelAdded() * $refuelRate;

        return round($parkingFee + $refuelFee, 2);
    }

    public function getFuelAdded(): float
    {
        $this->fuelAdded = $this->fuelAdded ?? $this->calculateFuelAdded();
        return $this->fuelAdded;
    }

    protected function calculateFuelAdded(): float
    {
        $fuelAdded = 0;

        $refuelThreshold = Config::get('car.refuel_threshold');
        $fuelLevel = $this->fuel->getLevel();
        $capacity = $this->getfuel()->getCapacity();

        if ($fuelLevel <= $refuelThreshold) {
            $fuelAdded = (1 - $fuelLevel) * $capacity;
        }

        return round($fuelAdded, 2);
    }
}
