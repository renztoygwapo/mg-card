<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointSystem extends Model
{
    protected $fillable = ['customer_group_id','product_id','equivalent_points'];

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
