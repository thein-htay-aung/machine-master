<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'date',
        'in_qty',
        'out_qty',
        'stock_qty',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Part::class, 'item_id');
    }
}
