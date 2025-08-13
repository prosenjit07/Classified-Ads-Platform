<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $category1 = Category::factory()->create(['name' => 'Electronics', 'status' => 'active']);
        $category2 = Category::factory()->create(['name' => 'Clothing', 'status' => 'active']);
        
        $brand1 = Brand::factory()->create(['name' => 'Sony', 'status' => 'active']);
        $brand2 = Brand::factory()->create(['name' => 'Nike', 'status' => 'active']);
        
        // Create products with different attributes
        Product::factory()->create([
            'name' => 'Sony Headphones',
            'category_id' => $category1->id,
            'brand_id' => $brand1->id,
            'price' => 199.99,
            'condition' => 'new',
            'status' => 'active',
        ]);
        
        Product::factory()->create([
            'name' => 'Nike Running Shoes',
            'category_id' => $category2->id,
            'brand_id' => $brand2->id,
            'price' => 129.99,
            'condition' => 'new',
            'status' => 'active',
        ]);
        
        Product::factory()->create([
            'name' => 'Used Smartphone',
            'category_id' => $category1->id,
            'brand_id' => $brand1->id,
            'price' => 299.99,
            'sale_price' => 249.99,
            'condition' => 'used',
            'status' => 'active',
        ]);
    }
    
    /** @test */
    public function it_can_filter_products_by_category()
    {
        $category = Category::where('name', 'Electronics')->first();
        
        $response = $this->getJson("/api/products?category_id={$category->id}");
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data') // Should return 2 electronics products
            ->assertJsonFragment(['name' => 'Sony Headphones'])
            ->assertJsonFragment(['name' => 'Used Smartphone'])
            ->assertJsonMissing(['name' => 'Nike Running Shoes']);
    }
    
    /** @test */
    public function it_can_filter_products_by_brand()
    {
        $brand = Brand::where('name', 'Sony')->first();
        
        $response = $this->getJson("/api/products?brand_id={$brand->id}");
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data') // Should return 2 Sony products
            ->assertJsonFragment(['name' => 'Sony Headphones'])
            ->assertJsonFragment(['name' => 'Used Smartphone']);
    }
    
    /** @test */
    public function it_can_filter_products_by_price_range()
    {
        $response = $this->getJson('/api/products?min_price=100&max_price=200');
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data') // Should return products in price range 100-200
            ->assertJsonFragment(['name' => 'Sony Headphones'])
            ->assertJsonFragment(['name' => 'Nike Running Shoes'])
            ->assertJsonMissing(['name' => 'Used Smartphone']);
    }
    
    /** @test */
    public function it_can_filter_products_by_condition()
    {
        $response = $this->getJson('/api/products?condition=used');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Used Smartphone'])
            ->assertJsonFragment(['condition' => 'used']);
    }
    
    /** @test */
    public function it_can_search_products()
    {
        $response = $this->getJson('/api/products?search=headphones');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Sony Headphones']);
    }
    
    /** @test */
    public function it_can_sort_products()
    {
        // Test price ascending
        $response = $this->getJson('/api/products?sort_by=price_asc');
        
        $products = $response->json('data');
        $this->assertLessThanOrEqual($products[1]['price'], $products[0]['price']);
        
        // Test price descending
        $response = $this->getJson('/api/products?sort_by=price_desc');
        
        $products = $response->json('data');
        $this->assertGreaterThanOrEqual($products[1]['price'], $products[0]['price']);
    }
    
    /** @test */
    public function it_returns_validation_errors_for_invalid_filters()
    {
        $response = $this->getJson('/api/products?min_price=invalid&max_price=abc');
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['min_price', 'max_price']);
    }
}
