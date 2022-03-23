<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerLog extends Model
{
    protected $fillable = ['customer_id', 'shift', 'encoded_by'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
