<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productService;
    protected $product;
    protected $category;
    protected $brand;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productService = app(ProductService::class);
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create();
        $this->user = User::factory()->create();
        
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 100.00,
            'sale_price' => 80.00,
            'is_featured' => true
        ]);
    }

    /** @test */
    public function it_can_get_product_by_slug()
    {
        // Add product to user's wishlist
        $this->user->wishlist()->create([
            'product_id' => $this->product->id
        ]);
        
        $result = $this->productService->getProductBySlug($this->product->slug);
        
        $this->assertEquals($this->product->id, $result['product']->id);
        $this->assertArrayHasKey('in_wishlist', $result);
        $this->assertEquals('100.00', $result['formatted_price']);
        $this->assertEquals('80.00', $result['formatted_sale_price']);
    }

    /** @test */
    public function it_throws_exception_for_nonexistent_slug()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->productService->getProductBySlug('nonexistent-slug');
    }

    /** @test */
    public function it_can_get_related_products()
    {
        // Create related products in the same category
        $relatedProducts = Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);
        
        $related = $this->productService->getRelatedProducts($this->product);
        
        $this->assertCount(4, $related); // Default limit is 4
        $this->assertNotContains($this->product->id, $related->pluck('id'));
    }

    /** @test */
    public function it_can_get_featured_products()
    {
        // Create featured products
        $featuredProducts = Product::factory()->count(3)->create([
            'is_featured' => true
        ]);
        
        $featured = $this->productService->getFeaturedProducts(2);
        
        $this->assertCount(2, $featured);
        $this->assertTrue($featured->every(function ($product) {
            return $product->is_featured === true;
        }));
    }

    /** @test */
    public function it_can_filter_products()
    {
        // Create test data
        $expensiveProduct = Product::factory()->create([
            'price' => 200.00,
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);
        
        $cheapProduct = Product::factory()->create([
            'price' => 50.00,
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);
        
        // Test price filter
        $filters = [
            'min_price' => 100,
            'max_price' => 300
        ];
        
        $products = $this->productService->getFilteredProducts($filters)->getCollection();
        
        // We should have both expensive product and the original product
        $this->assertCount(2, $products);
        $this->assertContains($expensiveProduct->id, $products->pluck('id'));
        $this->assertNotContains($cheapProduct->id, $products->pluck('id'));
        
        // Test category filter
        $newCategory = Category::factory()->create();
        $newCategoryProduct = Product::factory()->create([
            'category_id' => $newCategory->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);
        
        $filters = [
            'category' => $newCategory->slug
        ];
        
        $products = $this->productService->getFilteredProducts($filters);
        
        $this->assertCount(1, $products);
        $this->assertEquals($newCategoryProduct->id, $products->first()->id);
        
        // Test brand filter
        $filters = [
            'brand' => $this->brand->slug
        ];
        
        $products = $this->productService->getFilteredProducts($filters);
        
        $this->assertTrue($products->count() >= 3); // At least our 3 test products
        $this->assertTrue($products->every(function ($product) {
            return $product->brand_id === $this->brand->id;
        }));
        
        // Test search
        $uniqueName = 'Unique Product Name ' . uniqid();
        $searchedProduct = Product::factory()->create([
            'name' => $uniqueName,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'active'
        ]);
        
        $filters = [
            'search' => $uniqueName
        ];
        
        $products = $this->productService->getFilteredProducts($filters);
        
        $this->assertCount(1, $products);
        $this->assertEquals($searchedProduct->id, $products->first()->id);
    }

    /** @test */
    public function it_can_sort_products()
    {
        // Delete existing test products to avoid interference
        Product::query()->delete();
        
        // Create test products with different prices and creation dates
        $products = [
            ['price' => 150.00, 'created_at' => now()->subDays(3)],
            ['price' => 50.00, 'created_at' => now()->subDays(1)],
            ['price' => 200.00, 'created_at' => now()->subDays(5)],
            ['price' => 100.00, 'created_at' => now()->subDays(2)]
        ];
        
        foreach ($products as $productData) {
            Product::factory()->create([
                'price' => $productData['price'],
                'created_at' => $productData['created_at'],
                'brand_id' => $this->brand->id,
                'category_id' => $this->category->id,
                'status' => 'active'
            ]);
        }
        
        // Test price low to high
        $filters = [
            'sort' => 'price_asc'
        ];
        
        $products = $this->productService->getFilteredProducts($filters, 10);
        $prices = $products->pluck('price')->toArray();
        
        // Check if prices are sorted in ascending order
        $sortedPrices = $prices;
        sort($sortedPrices);
        $this->assertEquals($sortedPrices, $prices);
        
        // Test price high to low
        $filters = [
            'sort' => 'price_desc'
        ];
        
        $products = $this->productService->getFilteredProducts($filters, 10);
        $prices = $products->pluck('price')->toArray();
        
        // Check if prices are sorted in descending order
        $sortedPrices = $prices;
        rsort($sortedPrices);
        $this->assertEquals($sortedPrices, $prices);
        
        // Test newest first
        $filters = [
            'sort' => 'newest'
        ];
        
        $products = $this->productService->getFilteredProducts($filters, 10);
        $createdAts = $products->pluck('created_at')->map(function ($date) {
            return $date->format('Y-m-d H:i:s');
        })->toArray();
        
        // Sort the created_at timestamps in descending order
        $sortedCreatedAts = $createdAts;
        rsort($sortedCreatedAts);
        
        $this->assertEquals($sortedCreatedAts, $createdAts);
    }
}
