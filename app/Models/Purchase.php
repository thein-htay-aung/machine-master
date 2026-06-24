<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'invoice',
        'part_id',
        'price',
        'qty',
        'amount',
        'remark',
        'purchased_date',
        'purchase_by',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'purchased_date' => 'date',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
