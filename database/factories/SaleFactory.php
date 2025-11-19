<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'date' => $this->faker->date(),
            'date_finish' => $this->faker->date(),
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
        ];
    }
}
