<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedeemLog extends Model
{
    protected $fillable = [
        'redeem_id',
        'encoded_by',
        'customer_id',
        'amount',
        'serial_no',
        'shift',
        'tax',
        'redeem_type'
    ];

    public function redeem()
    {
        return $this->belongsTo(Redeem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
