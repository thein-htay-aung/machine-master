<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use App\Models\Machine;
use App\Models\Plant;
use App\Models\Purchase;
use App\Models\Issue;
use App\Models\StockAdjustment;
use App\Models\CurrentStock;
use App\Models\DailyStock;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'brand',
        'location',
        'plant_id',
        'category_id',
        'is_active',
        'unit_id',
        'min_qty',
        'image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_part')->withPivot('quantity', 'notes')->withTimestamps();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function currentStock()
    {
        return $this->hasOne(CurrentStock::class, 'item_id');
    }

    public function dailyStocks()
    {
        return $this->hasMany(DailyStock::class, 'item_id');
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
