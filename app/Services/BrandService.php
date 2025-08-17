<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandService
{
    /**
     * Get paginated list of brands
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getBrands(array $filters = [])
    {
        return Brand::query()
            ->withCount('products')
            ->latest()
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            })
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Create a new brand
     *
     * @param array $data
     * @param UploadedFile|null $logo
     * @return Brand
     */
    public function createBrand(array $data, ?UploadedFile $logo = null)
    {
        return DB::transaction(function () use ($data, $logo) {
            $data['slug'] = Str::slug($data['name']);
            
            if ($logo) {
                $data['logo'] = $this->uploadLogo($logo);
            }

            return Brand::create($data);
        });
    }

    /**
     * Update an existing brand
     *
     * @param Brand $brand
     * @param array $data
     * @param UploadedFile|null $logo
     * @param bool $deleteLogo
     * @return Brand
     */
    public function updateBrand(Brand $brand, array $data, ?UploadedFile $logo = null, bool $deleteLogo = false)
    {
        return DB::transaction(function () use ($brand, $data, $logo, $deleteLogo) {
            // Handle logo deletion if requested
            if ($deleteLogo && $brand->logo) {
                $this->deleteLogo($brand->logo);
                $data['logo'] = null;
            }
            // Handle new logo upload
            elseif ($logo) {
                // Delete old logo if exists
                if ($brand->logo) {
                    $this->deleteLogo($brand->logo);
                }
                $data['logo'] = $this->uploadLogo($logo);
            }
            // If no logo in request, keep the existing one
            else {
                unset($data['logo']);
            }

            $brand->update($data);
            return $brand;
        });
    }

    /**
     * Delete a brand
     *
     * @param Brand $brand
     * @return bool
     */
    public function deleteBrand(Brand $brand): bool
    {
        try {
            // Delete the brand's logo if it exists
            if ($brand->logo) {
                $this->deleteLogo($brand->logo);
            }
            
            // Force delete to completely remove the record
            return $brand->forceDelete();
        } catch (\Exception $e) {
            Log::error('Failed to delete brand: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload brand logo
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function uploadLogo(UploadedFile $file): string
    {
        return $file->store('brands', 'public');
    }

    /**
     * Delete a brand's logo from storage.
     *
     * @param string|null $logoPath
     * @return bool
     */
    public function deleteLogo(?string $logoPath): bool
    {
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return Storage::disk('public')->delete($logoPath);
        }
        return false;
    }
    
    /**
     * Get all active brands.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBrands()
    {
        return Brand::where('is_active', true)->get();
    }
    
    /**
     * Get brands with their product counts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBrandsWithProductCounts()
    {
        return Brand::withCount('products')->get();
    }

    /**
     * Get all brands for dropdown/select
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBrandsForSelect()
    {
        return Brand::orderBy('name')->get(['id', 'name']);
    }
    
    /**
     * Find a brand by ID.
     *
     * @param int $id
     * @return Brand|null
     */
    public function getBrandById(int $id)
    {
        return Brand::find($id);
    }
    
    /**
     * Get all brands.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBrands()
    {
        return Brand::all();
    }
}
