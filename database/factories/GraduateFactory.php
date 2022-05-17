<?php
//! NOTE: THIS FACTORY IS NO LONGER BEING USED

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GraduateFactory extends Factory
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
            'level' => $this->faker->randomElement(['master', 'bachelor', "phD"]),
        ];
    }
}