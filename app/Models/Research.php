<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Research extends Model{
  protected $fillable = [
    'code',
    'title',
    'budget',
    'fin_type',
    'fin_company',
    'date_init',
    'date_end',
    'external',
    'incentive',
    'grade',
    'type_research',
    'document',
    'plan',
    'objectives',
    'organization_id',
    'program_id',
    'group_id',
    'line_id',
    'research_authors',
    'role',
    'location',
    'status',
    'own_research',
    'research_state_id'
    ];
}