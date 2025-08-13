<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'attribute_name',
        'attribute_value',
        'attribute_type',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_value' => 'string',
        'order' => 'integer',
    ];

    /**
     * The default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'attribute_type' => 'text',
        'order' => 0,
    ];

    /**
     * Get the product that owns the detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the casted attribute value based on the attribute type.
     *
     * @return mixed
     */
    public function getCastedValueAttribute()
    {
        return match ($this->attribute_type) {
            'number' => (float) $this->attribute_value,
            'boolean' => (bool) $this->attribute_value,
            'integer' => (int) $this->attribute_value,
            'json' => json_decode($this->attribute_value, true),
            default => $this->attribute_value,
        };
    }

    /**
     * Set the attribute value with proper casting.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setAttributeValueAttribute($value)
    {
        $this->attributes['attribute_value'] = match ($this->attribute_type) {
            'number', 'integer' => (string) $value,
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }
}
