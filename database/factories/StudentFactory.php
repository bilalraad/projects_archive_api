<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name_ar' => $this->faker->name(),
            'name_en' => $this->faker->name(),
            'year_number' => $this->faker->randomElement([1, 2, 3, 4]),
            'level' => $this->faker->randomElement(['master', 'bachelor', "phD"]),
        ];
    }
}