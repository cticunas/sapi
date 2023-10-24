<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchAuthor extends Model
{
    protected $fillable = ['author_id','research_id','status','role'];
}