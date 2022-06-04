<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SpendDetail;

class SpendDetailFactory extends Factory
{
    protected $model = SpendDetail::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'day' => $this->faker->numberBetween(1, 31),
            'total' => $this->faker->randomNumber(5, true),
            'description' => $this->faker->sentence,
        ];
    }
}
