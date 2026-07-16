<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Issue extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'issue_no',
        'part_id',
        'plant_id',
        'qty',
        'price',
        'amount',
        'remark',
        'issued_date',
        'issue_by',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'issued_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

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
