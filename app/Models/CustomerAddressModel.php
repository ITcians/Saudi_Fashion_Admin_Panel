<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddressModel extends Model
{
    use HasFactory;
    protected $table = 'customer_address';

    protected $fillable = [
        'address',
        'customer_id',
        'address_category',
        // 'city',
        // 'district',
        // 'postal_code',
        'status',
    ];

}
