<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenerateTemplate extends Model
{
    protected $fillable = [
        'date',
        'premium',
        'unleaded',
        'desiel',
        'total'
    ];
}
