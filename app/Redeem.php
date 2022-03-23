<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Redeem extends Model
{
    protected $fillable = [
        'customer_id',
        'amount',
        'tax',
        'serial_no',
        'shift',
        'redeem_type'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class); 
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
