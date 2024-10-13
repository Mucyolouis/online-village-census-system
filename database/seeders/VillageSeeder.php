<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            $villages = [
                ['Gashiha', 140],
                ['Iriba', 140],
                ['Multimedia', 140],
                ['Umunyinya', 140],
                ['Umuremure', 140],
                ['Urugero', 140],
                ['Gasave', 32],
                ['Isoko', 32],
                ['Karisimbi', 32],
                ['Kicukiro', 32],
                ['Triangle', 32],
                ['Ubumwe', 32],
                ['Ahitegeye', 33],
                ['Intaho', 33],
                ['Iriba', 33],
                ['Isangano', 33],
                ['Urugero', 33],
            ];
    
            foreach ($villages as $village) {
                DB::table('villages')->insert([
                    'name' => $village[0],
                    'cell_id' => $village[1],
                ]);
            }
        }
    }
}
