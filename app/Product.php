<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_name','product_type'];

    public function price()
    {
        return $this->hasOne(Price::class);
    }

    public function product_transactions()
    {
        return $this->hasMany(Transaction::class); 
    }

    public function pointSystem()
    {
        return $this->hasOne(PointSystem::class); 
    }
}
