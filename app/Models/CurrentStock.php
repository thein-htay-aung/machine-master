<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentStock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'qty',
        'last_purchase_price',
    ];

    protected $casts = [
        'last_purchase_price' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Part::class, 'item_id');
    }
}
