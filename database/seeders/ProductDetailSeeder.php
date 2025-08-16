<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Database\Seeder;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        $productAttributes = [
            'electronics' => [
                ['attribute_name' => 'Screen Size', 'attribute_value' => '6.1 inches', 'attribute_type' => 'text'],
                ['attribute_name' => 'Storage', 'attribute_value' => '128GB', 'attribute_type' => 'text'],
                ['attribute_name' => 'RAM', 'attribute_value' => '8GB', 'attribute_type' => 'text'],
                ['attribute_name' => 'Battery Life', 'attribute_value' => '24 hours', 'attribute_type' => 'text'],
                ['attribute_name' => 'Operating System', 'attribute_value' => 'iOS 17', 'attribute_type' => 'text'],
                ['attribute_name' => 'Camera', 'attribute_value' => '48MP Main Camera', 'attribute_type' => 'text'],
                ['attribute_name' => 'Connectivity', 'attribute_value' => '5G, Wi-Fi 6E, Bluetooth 5.3', 'attribute_type' => 'text'],
                ['attribute_name' => 'Weight', 'attribute_value' => '187g', 'attribute_type' => 'text'],
                ['attribute_name' => 'Color Options', 'attribute_value' => 'Space Black, Silver, Gold, Deep Purple', 'attribute_type' => 'text'],
                ['attribute_name' => 'Warranty', 'attribute_value' => '1 Year Limited Warranty', 'attribute_type' => 'text'],
            ],
            'vehicles' => [
                ['attribute_name' => 'Engine', 'attribute_value' => '2.5L 4-Cylinder', 'attribute_type' => 'text'],
                ['attribute_name' => 'Transmission', 'attribute_value' => 'CVT Automatic', 'attribute_type' => 'text'],
                ['attribute_name' => 'Fuel Economy', 'attribute_value' => '32 MPG Combined', 'attribute_type' => 'text'],
                ['attribute_name' => 'Drivetrain', 'attribute_value' => 'Front-Wheel Drive', 'attribute_type' => 'text'],
                ['attribute_name' => 'Seating Capacity', 'attribute_value' => '5 Passengers', 'attribute_type' => 'number'],
                ['attribute_name' => 'Safety Rating', 'attribute_value' => '5-Star NHTSA', 'attribute_type' => 'text'],
                ['attribute_name' => 'Cargo Space', 'attribute_value' => '15.1 cubic feet', 'attribute_type' => 'text'],
                ['attribute_name' => 'Exterior Color', 'attribute_value' => 'Midnight Black Metallic', 'attribute_type' => 'text'],
                ['attribute_name' => 'Interior Color', 'attribute_value' => 'Black Fabric', 'attribute_type' => 'text'],
                ['attribute_name' => 'Warranty', 'attribute_value' => '3 Year/36,000 Mile Basic', 'attribute_type' => 'text'],
            ],
            'gaming' => [
                ['attribute_name' => 'Storage', 'attribute_value' => '825GB SSD', 'attribute_type' => 'text'],
                ['attribute_name' => 'Resolution', 'attribute_value' => '4K UHD', 'attribute_type' => 'text'],
                ['attribute_name' => 'Frame Rate', 'attribute_value' => 'Up to 120fps', 'attribute_type' => 'text'],
                ['attribute_name' => 'Ray Tracing', 'attribute_value' => 'Hardware Ray Tracing', 'attribute_type' => 'text'],
                ['attribute_name' => 'Audio', 'attribute_value' => '3D Audio Technology', 'attribute_type' => 'text'],
                ['attribute_name' => 'Controller', 'attribute_value' => 'DualSense Wireless Controller', 'attribute_type' => 'text'],
                ['attribute_name' => 'Connectivity', 'attribute_value' => 'Wi-Fi 6, Bluetooth 5.1, Ethernet', 'attribute_type' => 'text'],
                ['attribute_name' => 'Dimensions', 'attribute_value' => '390mm x 104mm x 260mm', 'attribute_type' => 'text'],
                ['attribute_name' => 'Weight', 'attribute_value' => '4.5kg', 'attribute_type' => 'text'],
                ['attribute_name' => 'Warranty', 'attribute_value' => '1 Year Limited Warranty', 'attribute_type' => 'text'],
            ],
        ];

        foreach ($products as $product) {
            // Determine product type based on name/category
            $attributeSet = 'electronics'; // default
            
            if (stripos($product->name, 'toyota') !== false || stripos($product->name, 'honda') !== false) {
                $attributeSet = 'vehicles';
            } elseif (stripos($product->name, 'playstation') !== false) {
                $attributeSet = 'gaming';
            }

            $attributes = $productAttributes[$attributeSet];
            
            // Customize attributes based on specific product
            if (stripos($product->name, 'macbook') !== false) {
                $attributes = [
                    ['attribute_name' => 'Screen Size', 'attribute_value' => '14.2 inches', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Processor', 'attribute_value' => 'Apple M3 Chip', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Memory', 'attribute_value' => '16GB Unified Memory', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Storage', 'attribute_value' => '512GB SSD', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Display', 'attribute_value' => 'Liquid Retina XDR', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Battery Life', 'attribute_value' => 'Up to 18 hours', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Weight', 'attribute_value' => '1.6kg', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Operating System', 'attribute_value' => 'macOS Sonoma', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Ports', 'attribute_value' => '3x Thunderbolt 4, HDMI, SDXC, MagSafe 3', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Warranty', 'attribute_value' => '1 Year Limited Warranty', 'attribute_type' => 'text'],
                ];
            } elseif (stripos($product->name, 'dell') !== false) {
                $attributes = [
                    ['attribute_name' => 'Screen Size', 'attribute_value' => '13.4 inches', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Processor', 'attribute_value' => 'Intel Core i7-1360P', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Memory', 'attribute_value' => '16GB LPDDR5', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Storage', 'attribute_value' => '512GB PCIe SSD', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Display', 'attribute_value' => 'InfinityEdge Touch Display', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Graphics', 'attribute_value' => 'Intel Iris Xe Graphics', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Weight', 'attribute_value' => '1.24kg', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Operating System', 'attribute_value' => 'Windows 11 Home', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Battery Life', 'attribute_value' => 'Up to 12 hours', 'attribute_type' => 'text'],
                    ['attribute_name' => 'Warranty', 'attribute_value' => '1 Year Premium Support', 'attribute_type' => 'text'],
                ];
            }

            foreach ($attributes as $index => $attribute) {
                ProductDetail::create([
                    'product_id' => $product->id,
                    'attribute_name' => $attribute['attribute_name'],
                    'attribute_value' => $attribute['attribute_value'],
                    'attribute_type' => $attribute['attribute_type'],
                    'order' => $index + 1,
                ]);
            }
        }
    }
}
