<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'barcode',
        'cost_price',
        'selling_price',
        'stock',
        'image',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->barcode)) {
                $product->barcode = static::generateUniqueBarcode();
            }
        });

        static::updating(function (Product $product) {
            if (empty($product->barcode)) {
                $product->barcode = static::generateUniqueBarcode();
            }
        });
    }

    public static function generateUniqueBarcode(): string
    {
        do {
            $barcode = '20' . str_pad((string) mt_rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
        } while (static::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
