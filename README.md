# Parking Service

This service offers parking in addition to refueling to vehicles that require it, there are two employees who work on commission and get paid different rates. The system is responsible for assigning the workload equally between the two employees in a way that favours profit.

## Requirements

- Small cars pay a flat rate of $25 for parking and large vehicles pay $35.
- Every car with 10% or less fuel, will be refueled to maximum capacity and charged the fuel amount in addition to the parking fee.
- Employee A gets paid 11% commission over the final amount paid, while employee B gets paid 15%.
- Fuel has a fixed rate of $1.75/litre.

## Solution

To gain the most profit, the most profitable cars should be assigned to employees with less commission rate. To do so, cars need to get
sorted by profit descending and employees get sorted based on commission rate ascending. To assign equal workload we need to calculate
each employee's share. If number of cars is a factor of number of employees then the workload can be equally assigned.
For instance, with 10 cars and 2 employees, each of the employees will get assigned to exactly 5 cars. But if number of cars is not a factor
of number of employees, some have to be assigned more. In this condition, to maintain almost equal amount of work for all employees,
we will assign some employees exactly one more car to fulfill all jobs. The minimum and maximum of jobs assigned is based on the following formulas:

```
minimum = floor (car / employees)
maximum = ceil (car / employees)

number of employees who should be assigned max = cars - (minimum * employees)
```

So all employees will be assigned to at least minimum number of cars and some will get just one more (maximum). We will start assigning maximum 
number of most profitable cars to employees with less commission rate and when we have assigned enough, will assign the rest of the employees as much as minimum number.

This way if we have 11 cars and 3 employees, for example, the distribution will be (4,4,3).
Please note that if we assign maximum number as much as we can, then it will be more profitable but the workload won't be assigned equally.
For Example, if we have 7 cars and 5 employees, and we assign maximum as much as possible then the last employee won't get assigned to any car (2,2,2,1,0).
Whereas in the proposed solution the distribution of workload for this situation will be (2,2,1,1,1) and all employees will get assigned a car.
Similarly, for 21 cars and 4 employees the distribution will be (6,5,5,5) instead of (6,6,6,3).

## Assumptions

The interpretation of `equally assigned` can be tricky here. So my assumption was if not all get exact same number of cars assigned, it should be as close as possible.

## Implementation Decisions
Lumen 8 PHP micro-framework is used for implementation which is best for blazing fast APIs/Commands.
Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs/8.x).
It has been implemented as a command but in a way that can be moved to an API easily.

## Requirements to run the application
1. Git
2. Either of

   2.1 PHP 8 and composer
   2.2 Docker and docker-compose


## How to run
1. PHP 8 and composer

```
$ git clone https://github.com/mymtalebi/parking-service
$ composer i -o
$ php artisan workload:assign {cars}
```

2. Docker and docker-compose

```
$ git clone https://github.com/mymtalebi/parking-service
$ docker-compose up -d
$ docker exec -it parking-service php artisan workload:assign {cars}
```

> {cars} should be a json string of cars such as:
>
> '[{"licencePlate":"A","size":"large","fuel":{"capacity":57,"level":0.07}},{"licencePlate":"B","size":"large","fuel":{"capacity":66,"level":0.59}},{"licencePlate":"C","size":"large","fuel":{"capacity":54,"level":0.49}},{"licencePlate":"D","size":"large","fuel":{"capacity":79,"level":0.93}},{"licencePlate":"E","size":"large","fuel":{"capacity":94,"level":0.2}},{"licencePlate":"F","size":"large","fuel":{"capacity":57,"level":0.1}},{"licencePlate":"G","size":"small","fuel":{"capacity":56,"level":0.05}},{"licencePlate":"H","size":"small","fuel":{"capacity":61,"level":0.78}},{"licencePlate":"I","size":"small","fuel":{"capacity":60,"level":0.65}},{"licencePlate":"J","size":"large","fuel":{"capacity":63,"level":0.01}}]'

## Run tests
```
$ docker exec -it parking-service vendor/bin/phpunit
```

> Please note that written tests are just some samples to showcase my knowledge of writing tests. They do not provide full coverage for the application due to timing considerations.


## Improvements to be made
Some objects are instantiated within the code as they are value objects mostly and don't hold logic, but they can be injected through container factories.

Also, some validation on the input should be implemented.


## License
This app is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
