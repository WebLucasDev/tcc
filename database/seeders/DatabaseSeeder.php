<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            WorkHoursSeeder::class,
            CollaboratorSeeder::class,
            TimeTrackingSeeder::class,
            SolicitationSeeder::class,
        ]);
    }
}
