<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        PenggunaSeeder::class,
        SopirSeeder::class,
        MobilSeeder::class,
        OrderSeeder::class,
        OrderAssignmentSeeder::class,
        HistoryBekerjaSopirSeeder::class,
        ReviewSopirSeeder::class,
    ]);
    }
}