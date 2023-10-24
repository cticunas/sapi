<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    protected $fillable = [
        'type',
        'name',
        'date',
        'url',
        'doi',
        'indexed',
        'other_indexed',
        'journal',
        'files',
        'research_id',
        'status',
        'period',
        'period_type',
        'public',
        'approved',
        'reviewed',
        'reviewed_date',
        'reviewed_by',
        'approved_by',
        'approved_date',
    ];
}
