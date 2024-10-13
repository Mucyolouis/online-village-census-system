<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ['name' => 'Kigali City'],
            ['name' => 'Northern Province'],
            ['name' => 'Southern Province'],
            ['name' => 'Eastern Province'],
            ['name' => 'Western Province'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
