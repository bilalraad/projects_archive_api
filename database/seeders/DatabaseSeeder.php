<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProjectsTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(StudentsSeeder::class);
        $this->call(TeachersSeeder::class);
    }
}