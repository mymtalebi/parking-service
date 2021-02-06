<?php


namespace Tests\Unit\Models;


use App\Models\Car;
use App\Models\CarFuel;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CarTest extends TestCase
{
    public function carDataProvider()
    {
        return [
            'small car that needs refuel' => [
                'data' => [
                    'licencePlate' => 'PlateA',
                    'size' => 'small',
                    'capacity' => 40,
                    'level' => 0.2,
                ],
                'expectedValue' => 84,
            ],
            'small car that does not need refuel' => [
                'data' => [
                    'licencePlate' => 'PlateA',
                    'size' => 'small',
                    'capacity' => 40,
                    'level' => 0.5,
                ],
                'expectedValue' => 20,
            ],
            'large car that needs refuel' => [
                'data' => [
                    'licencePlate' => 'PlateA',
                    'size' => 'large',
                    'capacity' => 70,
                    'level' => 0.05,
                ],
                'expectedValue' => 163,
            ],
            'large car that does not need refuel' => [
                'data' => [
                    'licencePlate' => 'PlateA',
                    'size' => 'large',
                    'capacity' => 70,
                    'level' => 0.7,
                ],
                'expectedValue' => 30,
            ]
        ];
    }

    /**
     * @dataProvider carDataProvider
     */
    public function testGetProfitWillCalculateProfitOnFirstRunCorrectly(array $data, float $expectedValue)
    {
        $config = [
            'car.parking_fee.small' => 20,
            'car.parking_fee.large' => 30,
        ];

        Config::shouldReceive('get')
            ->with('car.parking_fee.'. $data['size'])
            ->andReturn($config['car.parking_fee.'. $data['size']])
            ->once();
        Config::shouldReceive('get')
            ->with('car.refuel_fee')
            ->andReturn(2)
            ->once();
        Config::shouldReceive('get')
            ->with('car.refuel_threshold')
            ->andReturn(0.2)
            ->once();

        $car = new Car(
            $data['licencePlate'],
            $data['size'],
            new CarFuel($data['capacity'], $data['level'])
        );

        $this->assertEquals($expectedValue, $car->getProfit());
    }

    public function testGetProfitOnlyCalculatesOnce()
    {
        $car = new Car(
            'PlateA',
            'small',
            new CarFuel(40, 0.15)
        );

        Config::shouldReceive('get')
            ->with('car.parking_fee.small')
            ->andReturn(30)
            ->once();
        Config::shouldReceive('get')
            ->with('car.refuel_fee')
            ->once();
        Config::shouldReceive('get')
            ->with('car.refuel_threshold')
            ->once();

        $car->getProfit();
        $this->assertEquals(30, $car->getProfit());

        // second call should not call any extra Config::get()
        $this->assertEquals(30, $car->getProfit());
    }
}
