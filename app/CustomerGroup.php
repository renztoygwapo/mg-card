<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = ['name', 'description'];

    public function pointSystem()
    {
        return $this->hasMany(PointSystem::class); 
    }

    public function fleetCard()
    {
        return $this->hasMany(FleetCard::class); 
    }
}
