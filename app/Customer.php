<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = ['points'];
    protected $fillable = [
        'reference_no',
        'middle_initial', 
        'first_name', 
        'last_name',
        'barcode', 
        'mobile_no', 
        'address',
        'birthdate', 
        'expire_at',
        'customer_group_id',
        'vehicles',
        'plate_no',
        'is_admin'
    ];

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function vehicles()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer_transactions()
    {
        return $this->hasMany(Transaction::class); 
    }

    public function customerLog()
    {
        return $this->belongsTo(CustomerLog::class);
    }

    public function redeem()
    {
        return $this->hasMany(Redeem::class);
    }
}
