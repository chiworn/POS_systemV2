<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_email',
        'store_phone',
        'store_address',
        'store_logo',
        'enable_tax',
        'tax_rate',
        'tax_name',
        'tax_number',
    ];

    protected $casts = [
        'enable_tax' => 'boolean',
        'tax_rate' => 'float',
    ];

    protected $appends = [
        'store_logo_url',
    ];

    /**
     * Accessor for store_logo_url.
     */
    public function getStoreLogoUrlAttribute(): ?string
    {
        if (!$this->store_logo) {
            return null;
        }

        if (str_starts_with($this->store_logo, 'http')) {
            return $this->store_logo;
        }

        return asset('storage/' . ltrim(str_replace('storage/', '', $this->store_logo), '/'));
    }

    /**
     * Get or create global settings instance.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'store_name' => 'Lucky Mart',
            'store_phone' => '012345678',
            'store_email' => 'info@luckymart.com',
            'store_address' => 'Phnom Penh',
        ]);
    }
}
