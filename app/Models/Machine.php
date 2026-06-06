<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plant;
use App\Models\Status;

class Machine extends Model
{
    /** @use HasFactory<\Database\Factories\MachineFactory> */
    use HasFactory;

    protected $fillable = [
        'control_no',
        'name',
        'brand',
        'model',
        'serial_no',
        'supplier',
        'arrived_date',
        'location',
        'dimension',
        'weight',
        'electrical',
        'is_fixed_asset',
        'remark',
        'plant_id',
        'status_id',
        'image',
    ];

    protected $casts = [
        'arrived_date' => 'date',
        'is_fixed_asset' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/machine-placeholder.svg');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
