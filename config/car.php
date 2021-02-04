<?php

use App\Models\Car;

return [
    'parking_fee' => [
        Car::CAR_SIZE_LARGE => 35,
        Car::CAR_SIZE_SMALL => 25,
    ],
    'refuel_fee' => 1.75, // in dollar per litre
    'refuel_threshold' => 0.10, // 10%
];
