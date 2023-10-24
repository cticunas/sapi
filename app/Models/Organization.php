<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable=["code",'name','level','abbreviation','creation','type','parent','status','parent_id'];
}
