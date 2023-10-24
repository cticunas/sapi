<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable=["code","name","url","type","indexed","status"];
}
