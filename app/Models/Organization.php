<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable=["code",'name','level','abbreviation','creation','type','parent','status','parent_id'];

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class,'parent_id', 'id');
    }
}
