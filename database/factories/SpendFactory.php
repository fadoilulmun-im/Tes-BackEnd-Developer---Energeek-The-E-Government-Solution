<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Spend;

class SpendFactory extends Factory
{
    protected $model = Spend::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'year' => $this->faker->numberBetween(1000, 2022),
            'month' => $this->faker->numberBetween(1, 12),
        ];
    }
}
