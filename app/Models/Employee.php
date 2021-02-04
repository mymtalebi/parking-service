<?php

namespace App\Models;


class Employee
{
    public function __construct(protected string $name, protected int $commissionRate)
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCommissionRate(): int
    {
        return $this->commissionRate;
    }
}
