<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Claass extends Model
{
    protected $table = 'claasses';

    protected $fillable = [
        'name','code','description','status','maximum_students'
    ];
}