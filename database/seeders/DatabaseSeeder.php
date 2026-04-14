<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Note to self:
     * - In production I might comment out the sample seeder.
     */
    public function run(): void
    {
        // Other seeders can go here later (roles, operators, etc.)
        $this->call([
            SampleEventsSeeder::class,
            ReadinessIndicatorsSeeder::class,   // ← Added
        ]);
    }
}