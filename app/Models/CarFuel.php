<?php

namespace App\Models;


class CarFuel
{
    public function __construct(protected int $capacity, protected float $level)
    {
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getLevel(): float
    {
        return $this->level;
    }
}
