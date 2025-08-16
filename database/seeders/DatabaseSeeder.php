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
        // Seed all tables in proper order
        $this->call([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\CategorySeeder::class,
            \Database\Seeders\BrandSeeder::class,
            \Database\Seeders\ProductSeeder::class,
            \Database\Seeders\ProductDetailSeeder::class,
            \Database\Seeders\CategoryFieldSeeder::class,
            \Database\Seeders\MediaSeeder::class,
            \Database\Seeders\WishlistSeeder::class,
        ]);
        
        $this->command->info('Database seeding completed successfully!');
    }
}
