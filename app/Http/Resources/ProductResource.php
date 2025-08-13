<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price' => (float) $this->effective_price,
            'is_on_sale' => $this->is_on_sale,
            'discount_percentage' => $this->discount_percentage,
            'sku' => $this->sku,
            'stock_quantity' => $this->when($this->manage_stock, $this->stock_quantity),
            'stock_status' => $this->stock_status,
            'manage_stock' => $this->manage_stock,
            'condition' => $this->condition,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'published_at' => $this->published_at,
            
            // Relationships
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            
            'brand' => $this->whenLoaded('brand', function () {
                return $this->brand ? [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ] : null;
            }),
            
            'details' => $this->whenLoaded('details', function () {
                return $this->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'key' => $detail->key,
                        'value' => $detail->value,
                        'order' => $detail->order,
                    ];
                });
            }),
            
            'media' => $this->whenLoaded('media', function () {
                return $this->getMedia('images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb' => $media->hasGeneratedConversion('thumb') 
                            ? $media->getUrl('thumb') 
                            : $media->getUrl(),
                        'preview' => $media->hasGeneratedConversion('preview') 
                            ? $media->getUrl('preview') 
                            : $media->getUrl(),
                        'order' => $media->order_column,
                    ];
                });
            }),
            
            // Additional attributes
            'in_wishlist' => $this->when(
                $request->user() && $request->user()->id,
                function () use ($request) {
                    return $this->isInUserWishlist($request->user()->id);
                },
                false
            ),
            
            'wishlist_count' => $this->whenCounted('wishlistItems', function () {
                return $this->wishlist_items_count;
            }),
            
            'primary_image' => $this->when($this->relationLoaded('media') && $this->media->isNotEmpty(), function () {
                $primaryMedia = $this->getFirstMedia('images');
                return $primaryMedia ? [
                    'url' => $primaryMedia->getUrl(),
                    'thumb' => $primaryMedia->hasGeneratedConversion('thumb') 
                        ? $primaryMedia->getUrl('thumb') 
                        : $primaryMedia->getUrl(),
                    'preview' => $primaryMedia->hasGeneratedConversion('preview') 
                        ? $primaryMedia->getUrl('preview') 
                        : $primaryMedia->getUrl(),
                ] : null;
            }),
        ];
    }
}
