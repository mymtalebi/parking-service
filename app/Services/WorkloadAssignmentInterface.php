<?php


namespace App\Services;


interface WorkloadAssignmentInterface
{
    public function assign(array $cars, array $employees): array;
}
