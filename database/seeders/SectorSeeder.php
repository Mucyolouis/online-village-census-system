<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //{
        $sectors = [
            ['Gitega', 1],
            ['Kanyinya', 1],
            ['Kigali', 1],
            ['Kimisagara', 1],
            ['Mageregere', 1],
            ['Muhima', 1],
            ['Nyakabanda', 1],
            ['Nyamirambo', 1],
            ['Nyarugenge', 1],
            ['Rwezamenyo', 1],
            ['Bumbogo', 2],
            ['Gatsata', 2],
            ['Gikomero', 2],
            ['Gisozi', 2],
            ['Jabana', 2],
            ['Jali', 2],
            ['Kacyiru', 2],
            ['Kimihurura', 2],
            ['Kimironko', 2],
            ['Kinyinya', 2],
            ['Ndera', 2],
            ['Nduba', 2],
            ['Remera', 2],
            ['Rusororo', 2],
            ['Rutunga', 2],
            ['Gahanga', 3],
            ['Gatenga', 3],
            ['Gikondo', 3],
            ['Kagarama', 3],
            ['Kanombe', 3],
            ['Kicukiro', 3],
            ['Kigarama', 3],
            ['Masaka', 3],
            ['Niboye', 3],
            ['Nyarugunga', 3],
        ];

        foreach ($sectors as $sector) {
            DB::table('sectors')->insert([
                'name' => $sector[0],
                'district_id' => $sector[1],
            ]);
        }
    }
}
