<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plant;
use App\Models\Status;
use App\Models\User;
use App\Models\Part;

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
        'currency',
        'unit_price',
        'is_fixed_asset',
        'remark',
        'plant_id',
        'status_id',
        'image',
        'created_by',
        'updated_by',
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

    public function parts()
    {
        return $this->belongsToMany(Part::class, 'machine_part')->withPivot('quantity', 'notes')->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
