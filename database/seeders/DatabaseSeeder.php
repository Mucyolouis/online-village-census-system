<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CellSeeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            DistrictSeeder::class,
            SectorSeeder::class,
            CellSeeder::class,
            VillageSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            //BannersTableSeeder::class,
            //BlogCategoriesTableSeeder::class,
            //BlogPostsTableSeeder::class,
        ]);

        Artisan::call('shield:generate --all');
    }
}
