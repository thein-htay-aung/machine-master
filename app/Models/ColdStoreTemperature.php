<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColdStoreTemperature extends Model
{
    use HasFactory;

    protected $fillable = [
        'cold_store',
        'temperature',
        'recorded_at',
    ];

    protected $casts = [
        'temperature' => 'float',
        'recorded_at' => 'datetime',
    ];
}
