<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable=["code","name","resolution","init","end","active","status"];
}
