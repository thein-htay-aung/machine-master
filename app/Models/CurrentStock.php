<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentStock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'qty',
    ];

    public function item()
    {
        return $this->belongsTo(Part::class, 'item_id');
    }
}
