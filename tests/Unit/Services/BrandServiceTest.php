<?php

namespace Tests\Unit\Services;

use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $brandService;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->brandService = app(BrandService::class);
        $this->brand = Brand::factory()->create();
        
        // Fake the storage
        Storage::fake('public');
    }

    /** @test */
    public function it_can_get_all_brands()
    {
        // Create some test brands
        $brands = Brand::factory()->count(3)->create();
        
        $result = $this->brandService->getAllBrands();
        
        $this->assertCount(4, $result); // 3 new + 1 from setUp
        $this->assertContains($this->brand->name, $result->pluck('name'));
    }

    /** @test */
    public function it_can_get_brand_by_id()
    {
        $foundBrand = $this->brandService->getBrandById($this->brand->id);
        
        $this->assertEquals($this->brand->id, $foundBrand->id);
        $this->assertEquals($this->brand->name, $foundBrand->name);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_brand()
    {
        $nonExistentId = 9999;
        $foundBrand = $this->brandService->getBrandById($nonExistentId);
        
        $this->assertNull($foundBrand);
    }

    /** @test */
    public function it_can_create_a_new_brand()
    {
        $data = [
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Test description',
            'website' => 'https://testbrand.com',
            'is_active' => true
        ];
        
        $createdBrand = $this->brandService->createBrand($data);
        
        $this->assertDatabaseHas('brands', [
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Test description',
            'website' => 'https://testbrand.com',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_can_update_an_existing_brand()
    {
        $data = [
            'name' => 'Updated Brand Name',
            'description' => 'Updated description',
            'is_active' => false
        ];
        
        $updatedBrand = $this->brandService->updateBrand($this->brand, $data);
        
        $this->assertEquals('Updated Brand Name', $updatedBrand->name);
        $this->assertEquals('Updated description', $updatedBrand->description);
        $this->assertFalse($updatedBrand->is_active);
    }

    /** @test */
    public function it_can_delete_a_brand()
    {
        // Create a new brand to delete
        $brand = Brand::factory()->create();
        
        $result = $this->brandService->deleteBrand($brand);
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    /** @test */
    public function it_can_upload_and_delete_logo()
    {
        // Using reflection to test protected method
        $reflection = new \ReflectionClass($this->brandService);
        $method = $reflection->getMethod('uploadLogo');
        $method->setAccessible(true);
        
        // Test logo upload
        $file = UploadedFile::fake()->image('logo.jpg');
        
        // Call the protected method
        $logoPath = $method->invoke($this->brandService, $file);
        
        // Assert the file was stored
        $this->assertTrue(Storage::disk('public')->exists($logoPath));
        
        // Test logo deletion
        $this->brandService->deleteLogo($logoPath);
        
        // Assert the file was deleted
        $this->assertFalse(Storage::disk('public')->exists($logoPath));
    }

    /** @test */
    public function it_can_get_active_brands()
    {
        // Create some active and inactive brands
        $activeBrands = Brand::factory()->count(2)->create(['is_active' => true]);
        $inactiveBrands = Brand::factory()->count(2)->create(['is_active' => false]);
        
        $result = $this->brandService->getActiveBrands();
        
        // Should only return active brands (2 new + 1 from setUp if active)
        $expectedCount = $this->brand->is_active ? 3 : 2;
        $this->assertCount($expectedCount, $result);
        
        // All returned brands should be active
        $this->assertTrue($result->every(function ($brand) {
            return $brand->is_active === true;
        }));
    }

    /** @test */
    public function it_can_get_brands_with_product_counts()
    {
        // Create brands with different product counts
        $brandWithProducts = Brand::factory()->hasProducts(3)->create();
        $brandWithoutProducts = Brand::factory()->create();
        
        $result = $this->brandService->getBrandsWithProductCounts();
        
        // Find our test brands in the result
        $brandWithProductsResult = $result->firstWhere('id', $brandWithProducts->id);
        $brandWithoutProductsResult = $result->firstWhere('id', $brandWithoutProducts->id);
        
        $this->assertEquals(3, $brandWithProductsResult->products_count);
        $this->assertEquals(0, $brandWithoutProductsResult->products_count);
    }
}
