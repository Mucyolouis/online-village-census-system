<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'name' => 'Nyarugenge',
                'province_id' => 1,
            ],
            [
                'name' => 'Gasabo',
                'province_id' => 1,
            ],
            [
                'name' => 'Kicukiro',
                'province_id' => 1,
            ],
            
        ];

        foreach ($districts as $district) {
            District::create($district);
        }
    }
}
