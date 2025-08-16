<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder creates sample media records
        // In a real application, you would typically associate media with actual files
        
        $mediaRecords = [
            [
                'model_type' => 'App\\Models\\Product',
                'model_id' => 1,
                'uuid' => Str::uuid(),
                'collection_name' => 'product_images',
                'name' => 'iPhone 15 Pro Main Image',
                'file_name' => 'iphone-15-pro-main.jpg',
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 1024000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'iPhone 15 Pro']),
                'generated_conversions' => json_encode(['thumb' => true, 'medium' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Product',
                'model_id' => 1,
                'uuid' => Str::uuid(),
                'collection_name' => 'product_gallery',
                'name' => 'iPhone 15 Pro Gallery 1',
                'file_name' => 'iphone-15-pro-gallery-1.jpg',
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 856000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'iPhone 15 Pro Gallery Image']),
                'generated_conversions' => json_encode(['thumb' => true, 'medium' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Product',
                'model_id' => 2,
                'uuid' => Str::uuid(),
                'collection_name' => 'product_images',
                'name' => 'Samsung Galaxy S24 Ultra Main Image',
                'file_name' => 'samsung-galaxy-s24-ultra-main.jpg',
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 1156000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'Samsung Galaxy S24 Ultra']),
                'generated_conversions' => json_encode(['thumb' => true, 'medium' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Brand',
                'model_id' => 1,
                'uuid' => Str::uuid(),
                'collection_name' => 'brand_logo',
                'name' => 'Apple Logo',
                'file_name' => 'apple-logo.png',
                'mime_type' => 'image/png',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 45000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'Apple Brand Logo']),
                'generated_conversions' => json_encode(['thumb' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Brand',
                'model_id' => 2,
                'uuid' => Str::uuid(),
                'collection_name' => 'brand_logo',
                'name' => 'Samsung Logo',
                'file_name' => 'samsung-logo.png',
                'mime_type' => 'image/png',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 38000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'Samsung Brand Logo']),
                'generated_conversions' => json_encode(['thumb' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'model_type' => 'App\\Models\\Category',
                'model_id' => 1,
                'uuid' => Str::uuid(),
                'collection_name' => 'category_image',
                'name' => 'Electronics Category Image',
                'file_name' => 'electronics-category.jpg',
                'mime_type' => 'image/jpeg',
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 672000,
                'manipulations' => json_encode([]),
                'custom_properties' => json_encode(['alt' => 'Electronics Category']),
                'generated_conversions' => json_encode(['thumb' => true, 'medium' => true]),
                'responsive_images' => json_encode([]),
                'order_column' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('media')->insert($mediaRecords);
    }
}
