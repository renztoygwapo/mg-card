<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointSystemSummary extends Model
{
    protected $fillable = ['point_system_id', 'encoded_by','shift','customer_group_id','product_id','equivalent_points'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function pointSystem()
    {
        return $this->belongsTo(PointSystem::class);
    }
}
