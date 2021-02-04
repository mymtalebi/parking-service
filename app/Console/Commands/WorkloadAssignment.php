<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Car;
use App\Models\CarFuel;
use App\Models\Employee;
use App\Services\WorkloadAssignmentInterface;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class WorkloadAssignment extends Command
{
    /**
     * @inheritdoc
     */
    protected $signature = "workload:assign {cars}";

    /**
     * @inheritdoc
     */
    protected $description = "Assigns workload for the list of cars";

    public function __construct(protected WorkloadAssignmentInterface $workloadAssigner, protected Container $container)
    {
        parent::__construct();
    }

    public function handle()
    {
        $data = $this->argument('cars');
        $cars = $this->extractCars($data);

        $employees = $this->getEmployees();

        $assignments = $this->workloadAssigner->assign($cars, $employees);

        $result = array_reduce($assignments, function (array $result, Assignment $assignment) {
            $result[] = $assignment->toArray();
            return $result;
        }, []);

        var_dump($result);
    }

    private function extractCars(?string $data): array
    {
        $data = json_decode($data, true);
        if (!$data) {
            throw new RuntimeException('Invalid input');
        }

        $cars = [];
        foreach ($data as $carInfo) {
            // TODO validate $carInfo
            $carFuel = new CarFuel($carInfo['fuel']['capacity'], $carInfo['fuel']['level']);
            $cars[] = new Car($carInfo['licencePlate'], $carInfo['size'], $carFuel);
        }

        return $cars;
    }

    private function getEmployees(): array
    {
        $employees = [];

        $employeesData = Config::get('employee.list');
        foreach ($employeesData as $employee) {
            $employees[] = new Employee($employee['name'], $employee['commissionRate']);
        }

        return $employees;
    }
}
