<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'issue_no',
        'part_id',
        'qty',
        'remark',
        'issued_date',
        'issue_by',
        'created_by',
    ];

    protected $casts = [
        'issued_date' => 'date',
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
