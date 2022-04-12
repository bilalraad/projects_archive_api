<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'student_name' => $this->faker->name(),
            'student_phone_no' => $this->faker->phoneNumber(),
            'supervisor_name' => $this->faker->name(),
            'graduation_year' => $this->faker->date(),
            'abstract' => $this->faker->paragraph(),
            'level' => $this->faker->randomElement(['master', 'bachelor', "phD"]),
            'key_words' => $this->faker->randomElements(['x', 'y', 'z'], 2, true),
        ];
    }
}
