<?php

namespace App\Models;

use App\Events\InventoryUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'status',
    ];

    protected $dispatchesEvents = [
        'updated' => InventoryUpdated::class,
        'created' => InventoryUpdated::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
