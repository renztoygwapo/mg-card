<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FleetCard extends Model
{
    protected $fillable = ['company_name', 'company_address', 'company_number', 'customer_group_id', 'tin_no'];

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class); 
    }
}
