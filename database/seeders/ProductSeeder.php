<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories and brands
        $categories = Category::whereNotNull('parent_id')->get(); // Only subcategories
        $brands = Brand::where('is_active', true)->get();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->warn('Categories or Brands not found. Please run CategorySeeder and BrandSeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'The most advanced iPhone with titanium design, A17 Pro chip, and professional camera system.',
                'short_description' => 'Latest iPhone with titanium design and A17 Pro chip.',
                'price' => 999.00,
                'sale_price' => 949.00,
                'sku' => 'IPHONE15PRO001',
                'stock_quantity' => 50,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'published',
                'order' => 1,
                'meta_title' => 'iPhone 15 Pro - Latest Apple Smartphone',
                'meta_description' => 'Buy the latest iPhone 15 Pro with titanium design and advanced features.',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Premium Android smartphone with S Pen, advanced camera system, and powerful performance.',
                'short_description' => 'Premium Samsung smartphone with S Pen and advanced cameras.',
                'price' => 1199.00,
                'sale_price' => null,
                'sku' => 'SAMS24ULTRA001',
                'stock_quantity' => 30,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'published',
                'order' => 2,
                'meta_title' => 'Samsung Galaxy S24 Ultra - Premium Android Phone',
                'meta_description' => 'Experience the ultimate Android smartphone with Galaxy S24 Ultra.',
            ],
            [
                'name' => 'MacBook Pro 14-inch M3',
                'slug' => 'macbook-pro-14-m3',
                'description' => 'Professional laptop with M3 chip, Liquid Retina XDR display, and all-day battery life.',
                'short_description' => 'Professional MacBook with M3 chip and stunning display.',
                'price' => 1999.00,
                'sale_price' => 1899.00,
                'sku' => 'MBP14M3001',
                'stock_quantity' => 25,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'published',
                'order' => 3,
                'meta_title' => 'MacBook Pro 14-inch M3 - Professional Laptop',
                'meta_description' => 'Powerful MacBook Pro with M3 chip for professional workflows.',
            ],
            [
                'name' => 'Dell XPS 13',
                'slug' => 'dell-xps-13',
                'description' => 'Ultra-portable laptop with InfinityEdge display and premium build quality.',
                'short_description' => 'Ultra-portable Dell laptop with premium design.',
                'price' => 1299.00,
                'sale_price' => null,
                'sku' => 'DELLXPS13001',
                'stock_quantity' => 40,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => false,
                'status' => 'published',
                'order' => 4,
                'meta_title' => 'Dell XPS 13 - Ultra-portable Laptop',
                'meta_description' => 'Discover the Dell XPS 13 ultra-portable laptop with premium features.',
            ],
            [
                'name' => 'iPad Pro 12.9-inch',
                'slug' => 'ipad-pro-12-9',
                'description' => 'Professional tablet with M2 chip, Liquid Retina XDR display, and Apple Pencil support.',
                'short_description' => 'Professional iPad with M2 chip and stunning display.',
                'price' => 1099.00,
                'sale_price' => 999.00,
                'sku' => 'IPADPRO129001',
                'stock_quantity' => 35,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'published',
                'order' => 5,
                'meta_title' => 'iPad Pro 12.9-inch - Professional Tablet',
                'meta_description' => 'Experience professional productivity with iPad Pro 12.9-inch.',
            ],
            [
                'name' => 'Toyota Camry 2024',
                'slug' => 'toyota-camry-2024',
                'description' => 'Reliable midsize sedan with hybrid powertrain option and advanced safety features.',
                'short_description' => 'Reliable Toyota sedan with hybrid option.',
                'price' => 28500.00,
                'sale_price' => null,
                'sku' => 'TOYCAMRY2024001',
                'stock_quantity' => 10,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'published',
                'order' => 6,
                'meta_title' => 'Toyota Camry 2024 - Reliable Midsize Sedan',
                'meta_description' => 'Drive home in a reliable Toyota Camry 2024 sedan.',
            ],
            [
                'name' => 'Honda Civic 2024',
                'slug' => 'honda-civic-2024',
                'description' => 'Compact car with excellent fuel economy, modern design, and advanced technology.',
                'short_description' => 'Modern Honda compact car with great fuel economy.',
                'price' => 24500.00,
                'sale_price' => 23500.00,
                'sku' => 'HONCIVIC2024001',
                'stock_quantity' => 15,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => false,
                'status' => 'published',
                'order' => 7,
                'meta_title' => 'Honda Civic 2024 - Modern Compact Car',
                'meta_description' => 'Experience the modern Honda Civic 2024 with excellent features.',
            ],
            [
                'name' => 'Sony PlayStation 5',
                'slug' => 'sony-playstation-5',
                'description' => 'Next-generation gaming console with 4K gaming, ray tracing, and ultra-fast SSD.',
                'short_description' => 'Next-gen gaming console with 4K and ray tracing.',
                'price' => 499.00,
                'sale_price' => null,
                'sku' => 'SONYPS5001',
                'stock_quantity' => 20,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'condition' => 'new',
                'is_active' => true,
                'is_featured' => true,
                'status' => 'published',
                'order' => 8,
                'meta_title' => 'Sony PlayStation 5 - Next-Gen Gaming Console',
                'meta_description' => 'Experience next-generation gaming with PlayStation 5.',
            ],
        ];

        foreach ($products as $productData) {
            // Assign random category and brand
            $category = $categories->random();
            $brand = $brands->random();
            
            $productData['category_id'] = $category->id;
            $productData['brand_id'] = $brand->id;
            $productData['published_at'] = now();
            
            Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }
    }
}
