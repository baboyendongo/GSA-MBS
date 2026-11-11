<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ActivityStatesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(InstancelsSeeder::class);
        $this->call(InstancesSeeder::class);
        $this->call(ProjectB2BSeeder::class);
    }
}
