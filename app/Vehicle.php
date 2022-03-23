<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_name', 'description',
    ];

    public function vehicles()
    {
        return $this->hasMany(Customer::class);
    }
}
