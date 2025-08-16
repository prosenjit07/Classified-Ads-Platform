<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreshBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the brands table
        Brand::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'American multinational technology company',
                'website' => 'https://www.apple.com',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'active',
                'order' => 1,
                'meta_title' => 'Apple Products',
                'meta_description' => 'Discover the latest Apple products and accessories',
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'South Korean multinational electronics company',
                'website' => 'https://www.samsung.com',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'active',
                'order' => 2,
                'meta_title' => 'Samsung Electronics',
                'meta_description' => 'Explore Samsung smartphones, tablets, and electronics',
            ],
            [
                'name' => 'Google',
                'slug' => 'google',
                'description' => 'American multinational technology company',
                'website' => 'https://www.google.com',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'active',
                'order' => 3,
                'meta_title' => 'Google Products',
                'meta_description' => 'Google devices and services',
            ],
            [
                'name' => 'Microsoft',
                'slug' => 'microsoft',
                'description' => 'American multinational technology corporation',
                'website' => 'https://www.microsoft.com',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'active',
                'order' => 4,
                'meta_title' => 'Microsoft Products',
                'meta_description' => 'Microsoft software and hardware solutions',
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Japanese multinational conglomerate corporation',
                'website' => 'https://www.sony.com',
                'is_active' => true,
                'is_featured' => false,
                'status' => 'active',
                'order' => 5,
                'meta_title' => 'Sony Electronics',
                'meta_description' => 'Sony cameras, gaming consoles, and entertainment products',
            ],
            [
                'name' => 'Dell',
                'slug' => 'dell',
                'description' => 'American multinational computer technology company',
                'website' => 'https://www.dell.com',
                'is_active' => true,
                'is_featured' => false,
                'status' => 'active',
                'order' => 6,
                'meta_title' => 'Dell Computers',
                'meta_description' => 'Dell laptops, desktops, and computer accessories',
            ],
            [
                'name' => 'HP',
                'slug' => 'hp',
                'description' => 'American multinational information technology company',
                'website' => 'https://www.hp.com',
                'is_active' => true,
                'is_featured' => false,
                'status' => 'active',
                'order' => 7,
                'meta_title' => 'HP Products',
                'meta_description' => 'HP printers, laptops, and computing solutions',
            ],
            [
                'name' => 'Toyota',
                'slug' => 'toyota',
                'description' => 'Japanese multinational automotive manufacturer',
                'website' => 'https://www.toyota.com',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'active',
                'order' => 8,
                'meta_title' => 'Toyota Vehicles',
                'meta_description' => 'Toyota cars, trucks, and hybrid vehicles',
            ],
            [
                'name' => 'Honda',
                'slug' => 'honda',
                'description' => 'Japanese public multinational conglomerate manufacturer',
                'website' => 'https://www.honda.com',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'active',
                'order' => 9,
                'meta_title' => 'Honda Vehicles',
                'meta_description' => 'Honda cars, motorcycles, and power equipment',
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'American multinational corporation engaged in design and manufacturing of footwear',
                'website' => 'https://www.nike.com',
                'is_active' => true,
                'is_featured' => false,
                'status' => 'active',
                'order' => 10,
                'meta_title' => 'Nike Products',
                'meta_description' => 'Nike shoes, clothing, and sports equipment',
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }
        
        $this->command->info('Successfully seeded ' . count($brands) . ' brands.');
    }
}
