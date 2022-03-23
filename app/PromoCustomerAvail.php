<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoCustomerAvail extends Model
{
    protected $fillable = ['customer_id','promo_id','serial_no','isAvail','date_expired'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function promo_item()
    {
        return $this->belongsTo(PromoItem::class, 'promo_id');
    }
}
