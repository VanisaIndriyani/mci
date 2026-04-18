<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'delivery_number',
        'delivery_date',
        'shipped_quantity',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function documentArchives(): MorphMany
    {
        return $this->morphMany(DocumentArchive::class, 'documentable');
    }
}
