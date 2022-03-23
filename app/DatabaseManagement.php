<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatabaseManagement extends Model
{
    protected $fillable = [
        'user',
        'path',
        'shift'
    ];
}
