<?php


namespace Tests\Unit\Services;


use App\Models\Assignment;
use App\Models\Car;
use App\Models\Employee;
use App\Services\WorkloadAssignmentService;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class WorkloadAssignmentServiceTest extends TestCase
{
    public function workloadAssignmentProvider(): array
    {
        return [
            // Both employees will get exactly 2 cars assigned
            'Cars number is a factor of employees number hence, equal workload for all' => [
                'cars' => [
                    $this->createCarMock('PlateA', 25, 0),
                    $this->createCarMock('PlateB', 138.95, 59.4),
                    $this->createCarMock('PlateC', 35, 0),
                    $this->createCarMock('PlateD', 95.88, 40.5),
                ],
                'employees' => [
                    $this->createEmployeeMock('A', 10),
                    $this->createEmployeeMock('B', 20)
                ],
                'expectedValues' => [
                    [
                        'licencePlate' => 'PlateB',
                        'employee' => 'A',
                        'fuelAdded' => 59.4,
                        'price' => 138.95,
                    ],
                    [
                        'licencePlate' => 'PlateD',
                        'employee' => 'A',
                        'fuelAdded' => 40.5,
                        'price' => 95.88,
                    ],
                    [
                        'licencePlate' => 'PlateC',
                        'employee' => 'B',
                        'fuelAdded' => 0,
                        'price' => 35,
                    ],
                    [
                        'licencePlate' => 'PlateA',
                        'employee' => 'B',
                        'fuelAdded' => 0,
                        'price' => 25,
                    ],
                ],
            ],
            // 2 cars assigned to employee A, 2 to employee C and only one to employee B
            'Some will get exactly one more assignment to balance load almost equally' => [
                'cars' => [
                    $this->createCarMock('PlateA', 25, 0),
                    $this->createCarMock('PlateB', 138.95, 59.4),
                    $this->createCarMock('PlateC', 35, 0),
                    $this->createCarMock('PlateD', 95.88, 40.5),
                    $this->createCarMock('PlateE', 35, 0),
                ],
                'employees' => [
                    new Employee('A', 10),
                    new Employee('B', 30),
                    new Employee('C', 20),
                ],
                'expectedValues' => [
                    [
                        'licencePlate' => 'PlateB',
                        'employee' => 'A',
                        'fuelAdded' => 59.4,
                        'price' => 138.95,
                    ],
                    [
                        'licencePlate' => 'PlateD',
                        'employee' => 'A',
                        'fuelAdded' => 40.5,
                        'price' => 95.88,
                    ],
                    [
                        'licencePlate' => 'PlateE',
                        'employee' => 'C',
                        'fuelAdded' => 0,
                        'price' => 35,
                    ],
                    [
                        'licencePlate' => 'PlateC',
                        'employee' => 'C',
                        'fuelAdded' => 0,
                        'price' => 35,
                    ],
                    [
                        'licencePlate' => 'PlateA',
                        'employee' => 'B',
                        'fuelAdded' => 0,
                        'price' => 25,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider workloadAssignmentProvider
     */
    public function testAssignWillReturnEquallyAssignedWorkloadInMostProfitableOrder(array $cars, array $employees, array $expectedValues)
    {
        $service = new WorkloadAssignmentService();
        $assignments = $service->assign($cars, $employees);

        $this->assertContainsOnlyInstancesOf(Assignment::class, $assignments);

        $result = [];
        /** @var Assignement $assignment */
        foreach ($assignments as $assignment) {
            $result[] = $assignment->toArray();
        }

        $this->assertEquals($expectedValues, $result);
    }

    private function createCarMock(string $licencePlate, float $profit, float $fuelAdded): MockObject
    {
        $carMock = $this->getMockBuilder(Car::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLicencePlate', 'getProfit', 'getFuelAdded'])
            ->getMock();

        $carMock->expects($this->once())
            ->method('getLicencePlate')
            ->willReturn($licencePlate);

        // can be called a couple of times during sort
        $carMock->expects($this->any())
            ->method('getProfit')
            ->willReturn($profit);

        $carMock->expects($this->once())
            ->method('getFuelAdded')
            ->willReturn($fuelAdded);

        return $carMock;
    }

    private function createEmployeeMock(string $name, int $commissionRate): MockObject
    {
        $employeeMock = $this->getMockBuilder(Employee::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getName', 'getCommissionRate'])
            ->getMock();

        // can be called a couple of times for different assignments
        $employeeMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        // can be called a couple of times during sort
        $employeeMock->expects($this->any())
            ->method('getCommissionRate')
            ->willReturn($commissionRate);

        return $employeeMock;
    }
}
