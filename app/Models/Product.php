<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Tentukan kolom yang bisa diisi secara massal
    protected $fillable = [
        'name', 'slug', 'thumb_image', 'vendor_id', 'category_id', 'sub_category_id',
        'child_category_id', 'brand_id', 'jenis', 'qty', 'short_description', 'long_description',
        'video_link', 'sku', 'price', 'offer_price', 'offer_start_date', 'offer_end_date',
        'product_type', 'status', 'is_approved', 'seo_title', 'seo_description'
    ];

    // Tentukan tipe data kolom yang harus di-cast
    protected $casts = [
        'offer_start_date' => 'datetime',
        'offer_end_date' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productImageGalleries()
    {
        return $this->hasMany(ProductImageGallery::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
