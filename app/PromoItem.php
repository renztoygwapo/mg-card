<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoItem extends Model
{
    protected $fillable = ['name','eq_points','point_types','date_start','date_end'];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

}
