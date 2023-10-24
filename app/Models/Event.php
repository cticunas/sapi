<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [ 'title', 'description', 'date_init', 'date_end', 'status' ];
}
