<?php

namespace App\Models;


use Illuminate\Contracts\Support\Arrayable;

class Assignment implements Arrayable
{
    public function __construct(protected Car $car, protected Employee $employee)
    {
    }

    public function toArray(): array
    {
        return [
            'licencePlate' => $this->car->getLicencePlate(),
            'employee' => $this->employee->getName(),
            'fuelAdded' => $this->car->getFuelAdded(),
            'price' => $this->car->getProfit(),
        ];
    }
}
