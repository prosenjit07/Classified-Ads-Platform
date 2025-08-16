<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets',
                'meta_title' => 'Electronics - Latest Gadgets and Devices',
                'meta_description' => 'Browse our wide selection of electronic devices and gadgets',
                'status' => true,
                'is_featured' => true,
                'order' => 1,
                'children' => [
                    [
                        'name' => 'Mobile Phones',
                        'slug' => 'mobile-phones',
                        'description' => 'Smartphones and mobile devices',
                        'status' => true,
                        'order' => 1,
                    ],
                    [
                        'name' => 'Laptops',
                        'slug' => 'laptops',
                        'description' => 'Laptops and notebooks',
                        'status' => true,
                        'order' => 2,
                    ],
                    [
                        'name' => 'Tablets',
                        'slug' => 'tablets',
                        'description' => 'Tablets and iPads',
                        'status' => true,
                        'order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Vehicles',
                'slug' => 'vehicles',
                'description' => 'Cars, motorcycles, and other vehicles',
                'meta_title' => 'Vehicles - Cars, Bikes and More',
                'meta_description' => 'Find your perfect vehicle from our extensive collection',
                'status' => true,
                'is_featured' => true,
                'order' => 2,
                'children' => [
                    [
                        'name' => 'Cars',
                        'slug' => 'cars',
                        'description' => 'New and used cars',
                        'status' => true,
                        'order' => 1,
                    ],
                    [
                        'name' => 'Motorcycles',
                        'slug' => 'motorcycles',
                        'description' => 'Motorcycles and scooters',
                        'status' => true,
                        'order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'description' => 'Properties for sale and rent',
                'meta_title' => 'Real Estate - Properties for Sale and Rent',
                'meta_description' => 'Discover properties for sale and rent in your area',
                'status' => true,
                'is_featured' => true,
                'order' => 3,
                'children' => [
                    [
                        'name' => 'Houses',
                        'slug' => 'houses',
                        'description' => 'Houses for sale and rent',
                        'status' => true,
                        'order' => 1,
                    ],
                    [
                        'name' => 'Apartments',
                        'slug' => 'apartments',
                        'description' => 'Apartments and flats',
                        'status' => true,
                        'order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Home improvement and garden supplies',
                'status' => true,
                'order' => 4,
                'children' => [
                    [
                        'name' => 'Furniture',
                        'slug' => 'furniture',
                        'description' => 'Home and office furniture',
                        'status' => true,
                        'order' => 1,
                    ],
                    [
                        'name' => 'Garden Tools',
                        'slug' => 'garden-tools',
                        'description' => 'Gardening tools and equipment',
                        'status' => true,
                        'order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Clothing and accessories',
                'status' => true,
                'order' => 5,
                'children' => [
                    [
                        'name' => 'Men\'s Clothing',
                        'slug' => 'mens-clothing',
                        'description' => 'Men\'s fashion and clothing',
                        'status' => true,
                        'order' => 1,
                    ],
                    [
                        'name' => 'Women\'s Clothing',
                        'slug' => 'womens-clothing',
                        'description' => 'Women\'s fashion and clothing',
                        'status' => true,
                        'order' => 2,
                    ],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
            
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                Category::updateOrCreate(
                    ['slug' => $childData['slug']],
                    $childData
                );
            }
        }
    }
}
