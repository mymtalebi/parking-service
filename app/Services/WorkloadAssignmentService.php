<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Car;
use App\Models\Employee;

class WorkloadAssignmentService implements WorkloadAssignmentInterface
{
    public function assign(array $cars, array $employees): array
    {
        if (empty($cars) || empty($employees)) {
            return [];
        }

        // sort cars based on profit descending
        usort($cars, function (Car $carA, Car $carB) {
            return $carA->getProfit() > $carB->getProfit() ? -1 : 1;
        });

        // sort employees based on commission rate ascending
        usort($employees, function (Employee $employeeA, Employee $employeeB) {
            return $employeeA->getCommissionRate() > $employeeB->getCommissionRate() ? 1 : -1;
        });

        return $this->assignSortedInput($cars, $employees);
    }

    private function assignSortedInput(array &$cars, array &$employees): array
    {
        $employeeShare = count($cars) / count($employees);
        $maxEmployeeShare = ceil($employeeShare);
        $minEmployeeShare = floor($employeeShare);

        // Number of employees with higher share in case not all can get exact same number of assignment.
        // Each employee will get at least a minimum number of assignments and some will get exactly one more.
        $EmployeeWithHigherShareCount = count($cars) - (count($employees) * $minEmployeeShare);

        $employeeAssignmentCount = 0;
        $assignments = [];

        foreach ($cars as $car) {
            $employee = current($employees);

            $assignments[] = new Assignment($car, $employee);

            $employeeAssignmentCount++;
            $employeeShare = $EmployeeWithHigherShareCount > 0 ? $maxEmployeeShare : $minEmployeeShare;

            if ($employeeAssignmentCount >= $employeeShare) {
                next($employees);
                $employeeAssignmentCount = 0;

                $EmployeeWithHigherShareCount = $EmployeeWithHigherShareCount ? --$EmployeeWithHigherShareCount : 0;
            }
        }

        return $assignments;
    }
}
