<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OutcomeAuthor extends Model
{
    protected $fillable = ['role','status','outcome_id','author_id'];
}
