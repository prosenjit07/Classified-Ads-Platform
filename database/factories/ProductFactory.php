<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->sentence(3);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph,
            'short_description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sale_price' => $this->faker->optional(0.3)->randomFloat(2, 5, 800), // 30% chance of having a sale price
            'sku' => strtoupper(Str::random(8)),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'manage_stock' => $this->faker->boolean(80), // 80% chance of managing stock
            'stock_status' => $this->faker->randomElement(['in_stock', 'out_of_stock', 'on_backorder']),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'order' => $this->faker->numberBetween(0, 100),
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->paragraph,
        ];
    }

    /**
     * Indicate that the product is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the product is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Indicate that the product is in stock.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_status' => 'in_stock',
                'stock_quantity' => $this->faker->numberBetween(1, 100),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function outOfStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_status' => 'out_of_stock',
                'stock_quantity' => 0,
            ];
        });
    }

    /**
     * Indicate that the product is featured.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }

    /**
     * Indicate that the product is a bestseller.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function bestseller()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_bestseller' => true,
            ];
        });
    }

    /**
     * Indicate that the product is new.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function asNew()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_new' => true,
            ];
        });
    }

    /**
     * Indicate that the product is in a specific condition.
     *
     * @param  string  $condition
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function condition(string $condition)
    {
        return $this->state(function (array $attributes) use ($condition) {
            return [
                'condition' => $condition,
            ];
        });
    }

    /**
     * Indicate that the product belongs to a specific category.
     *
     * @param  \App\Models\Category|int  $category
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forCategory($category)
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category_id' => $category instanceof Category ? $category->id : $category,
            ];
        });
    }

    /**
     * Indicate that the product belongs to a specific brand.
     *
     * @param  \App\Models\Brand|int  $brand
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forBrand($brand)
    {
        return $this->state(function (array $attributes) use ($brand) {
            return [
                'brand_id' => $brand instanceof Brand ? $brand->id : $brand,
            ];
        });
    }
}