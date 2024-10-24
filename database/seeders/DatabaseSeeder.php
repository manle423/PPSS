<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductVariantSeeder::class);
        //$this->call(ProvinceDistrictSeeder::class);
        // $this->call(OrderSeeder::class);
        // $this->call(OrderItemSeeder::class);
        
    }
}
