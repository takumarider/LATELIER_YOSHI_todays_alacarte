<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'is_active',
        'sale_date',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sale_date'  => 'date:Y-m-d',
    ];

    protected $appends = ['image_url'];

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        $disk = config('filesystems.disks.public');

        if (! empty($disk['url'])) {
            return rtrim($disk['url'], '/') . '/' . ltrim($this->image, '/');
        }

        return Storage::disk('public')->url($this->image);
    }
}
