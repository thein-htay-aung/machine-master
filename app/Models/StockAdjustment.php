<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'adjustment_no',
        'part_id',
        'plant_id',
        'symbol',
        'qty',
        'price',
        'amount',
        'reason',
        'adjusted_date',
        'adjusted_by',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'adjusted_date' => 'date',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
