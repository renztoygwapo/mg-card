<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'customer_group_id',
        'liters',
        'price',
        'amount',
        'points',
        'shift'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class); 
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class); 
    }

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function redeem()
    {
        return $this->hasMany(Redeem::class);
    }
}
