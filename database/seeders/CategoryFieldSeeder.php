<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Database\Seeder;

class CategoryFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Define category-specific fields
        $categoryFields = [
            'electronics' => [
                ['name' => 'Brand', 'type' => 'select', 'options' => 'Apple,Samsung,Google,Sony,Microsoft', 'is_required' => true, 'order' => 1],
                ['name' => 'Model', 'type' => 'text', 'is_required' => true, 'order' => 2],
                ['name' => 'Condition', 'type' => 'select', 'options' => 'New,Used,Refurbished', 'is_required' => true, 'order' => 3],
                ['name' => 'Storage Capacity', 'type' => 'select', 'options' => '64GB,128GB,256GB,512GB,1TB', 'is_required' => false, 'order' => 4],
                ['name' => 'Color', 'type' => 'text', 'is_required' => false, 'order' => 5],
                ['name' => 'Warranty', 'type' => 'select', 'options' => 'No Warranty,6 Months,1 Year,2 Years', 'is_required' => false, 'order' => 6],
            ],
            'mobile-phones' => [
                ['name' => 'Operating System', 'type' => 'select', 'options' => 'iOS,Android,Other', 'is_required' => true, 'order' => 1],
                ['name' => 'Screen Size', 'type' => 'text', 'help_text' => 'Enter screen size in inches', 'is_required' => false, 'order' => 2],
                ['name' => 'RAM', 'type' => 'select', 'options' => '4GB,6GB,8GB,12GB,16GB', 'is_required' => false, 'order' => 3],
                ['name' => 'Camera', 'type' => 'text', 'help_text' => 'Main camera megapixels', 'is_required' => false, 'order' => 4],
                ['name' => 'Battery Capacity', 'type' => 'text', 'help_text' => 'Battery capacity in mAh', 'is_required' => false, 'order' => 5],
            ],
            'laptops' => [
                ['name' => 'Processor', 'type' => 'text', 'is_required' => true, 'order' => 1],
                ['name' => 'RAM', 'type' => 'select', 'options' => '4GB,8GB,16GB,32GB,64GB', 'is_required' => true, 'order' => 2],
                ['name' => 'Storage Type', 'type' => 'select', 'options' => 'HDD,SSD,Hybrid', 'is_required' => true, 'order' => 3],
                ['name' => 'Storage Size', 'type' => 'select', 'options' => '256GB,512GB,1TB,2TB', 'is_required' => true, 'order' => 4],
                ['name' => 'Graphics Card', 'type' => 'text', 'is_required' => false, 'order' => 5],
                ['name' => 'Screen Size', 'type' => 'select', 'options' => '13 inch,14 inch,15 inch,16 inch,17 inch', 'is_required' => false, 'order' => 6],
            ],
            'cars' => [
                ['name' => 'Make', 'type' => 'text', 'is_required' => true, 'order' => 1],
                ['name' => 'Model', 'type' => 'text', 'is_required' => true, 'order' => 2],
                ['name' => 'Year', 'type' => 'number', 'is_required' => true, 'order' => 3],
                ['name' => 'Mileage', 'type' => 'number', 'help_text' => 'Enter mileage in kilometers', 'is_required' => true, 'order' => 4],
                ['name' => 'Fuel Type', 'type' => 'select', 'options' => 'Petrol,Diesel,Electric,Hybrid,CNG', 'is_required' => true, 'order' => 5],
                ['name' => 'Transmission', 'type' => 'select', 'options' => 'Manual,Automatic,CVT', 'is_required' => true, 'order' => 6],
                ['name' => 'Body Type', 'type' => 'select', 'options' => 'Sedan,Hatchback,SUV,Coupe,Convertible,Wagon', 'is_required' => false, 'order' => 7],
                ['name' => 'Engine Size', 'type' => 'text', 'help_text' => 'Engine displacement (e.g., 2.0L)', 'is_required' => false, 'order' => 8],
                ['name' => 'Number of Owners', 'type' => 'select', 'options' => '1,2,3,4,5+', 'is_required' => false, 'order' => 9],
            ],
            'motorcycles' => [
                ['name' => 'Engine Capacity', 'type' => 'text', 'help_text' => 'Engine capacity in CC', 'is_required' => true, 'order' => 1],
                ['name' => 'Bike Type', 'type' => 'select', 'options' => 'Sport,Cruiser,Touring,Standard,Scooter', 'is_required' => true, 'order' => 2],
                ['name' => 'Fuel Tank Capacity', 'type' => 'text', 'help_text' => 'Fuel tank capacity in liters', 'is_required' => false, 'order' => 3],
                ['name' => 'Mileage', 'type' => 'text', 'help_text' => 'Mileage in km/l', 'is_required' => false, 'order' => 4],
            ],
            'houses' => [
                ['name' => 'Property Type', 'type' => 'select', 'options' => 'House,Villa,Townhouse,Bungalow', 'is_required' => true, 'order' => 1],
                ['name' => 'Bedrooms', 'type' => 'select', 'options' => '1,2,3,4,5,6+', 'is_required' => true, 'order' => 2],
                ['name' => 'Bathrooms', 'type' => 'select', 'options' => '1,2,3,4,5+', 'is_required' => true, 'order' => 3],
                ['name' => 'Area', 'type' => 'number', 'help_text' => 'Area in square feet', 'is_required' => true, 'order' => 4],
                ['name' => 'Parking', 'type' => 'select', 'options' => 'No Parking,1 Car,2 Cars,3+ Cars', 'is_required' => false, 'order' => 5],
                ['name' => 'Furnished', 'type' => 'select', 'options' => 'Unfurnished,Semi-Furnished,Fully Furnished', 'is_required' => false, 'order' => 6],
                ['name' => 'Floor', 'type' => 'text', 'help_text' => 'Floor number (e.g., Ground, 1st, 2nd)', 'is_required' => false, 'order' => 7],
            ],
            'apartments' => [
                ['name' => 'Apartment Type', 'type' => 'select', 'options' => 'Studio,1BHK,2BHK,3BHK,4BHK', 'is_required' => true, 'order' => 1],
                ['name' => 'Floor', 'type' => 'number', 'help_text' => 'Floor number', 'is_required' => true, 'order' => 2],
                ['name' => 'Total Floors', 'type' => 'number', 'help_text' => 'Total floors in building', 'is_required' => false, 'order' => 3],
                ['name' => 'Balcony', 'type' => 'select', 'options' => 'No Balcony,1 Balcony,2 Balconies,3+ Balconies', 'is_required' => false, 'order' => 4],
                ['name' => 'Elevator', 'type' => 'select', 'options' => 'Yes,No', 'is_required' => false, 'order' => 5],
            ],
            'furniture' => [
                ['name' => 'Material', 'type' => 'select', 'options' => 'Wood,Metal,Plastic,Glass,Fabric', 'is_required' => true, 'order' => 1],
                ['name' => 'Room', 'type' => 'select', 'options' => 'Living Room,Bedroom,Dining Room,Kitchen,Office', 'is_required' => true, 'order' => 2],
                ['name' => 'Dimensions', 'type' => 'text', 'help_text' => 'Length x Width x Height', 'is_required' => false, 'order' => 3],
                ['name' => 'Assembly Required', 'type' => 'select', 'options' => 'Yes,No', 'is_required' => false, 'order' => 4],
            ],
        ];

        foreach ($categories as $category) {
            $slug = $category->slug;
            
            if (isset($categoryFields[$slug])) {
                foreach ($categoryFields[$slug] as $fieldData) {
                    CategoryField::create([
                        'category_id' => $category->id,
                        'name' => $fieldData['name'],
                        'type' => $fieldData['type'],
                        'options' => $fieldData['options'] ?? null,
                        'is_required' => $fieldData['is_required'],
                        'order' => $fieldData['order'],
                        'help_text' => $fieldData['help_text'] ?? null,
                        'validation_rules' => null,
                        'default_value' => null,
                    ]);
                }
            }
        }
    }
}
