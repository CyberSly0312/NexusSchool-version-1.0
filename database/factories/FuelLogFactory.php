<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FuelLog>
 */
class FuelLogFactory extends Factory
{
    protected $model = FuelLog::class;

    public function definition(): array
    {
        $liters = $this->faker->randomFloat(2, 10, 80);
        $costPerLiter = $this->faker->randomFloat(2, 1, 3);

        return [
            'school_id' => '1',
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'liters' => $liters,
            'cost_per_liter' => $costPerLiter,
            'total_cost' => round($liters * $costPerLiter, 2),
            'fuel_type' => $this->faker->randomElement(['diesel', 'petrol']),
            'fuel_station' => $this->faker->company(),
            'odometer_reading' => $this->faker->numberBetween(10000, 200000),
            'fuel_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'receipt_document' => null,
            'notes' => null,
        ];
    }
}
