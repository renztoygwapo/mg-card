<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceSummary extends Model
{
    protected $fillable = ['encoded_by','price_id','product_date','shift','product_id','updated_product_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
