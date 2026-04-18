<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'po_date',
        'customer_name',
        'product_name',
        'quantity',
        'unit',
        'unit_price',
        'total_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function documentArchives(): MorphMany
    {
        return $this->morphMany(DocumentArchive::class, 'documentable');
    }
}
