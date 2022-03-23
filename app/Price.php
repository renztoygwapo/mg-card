<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['product_date','product_id','product_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
