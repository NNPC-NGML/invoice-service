<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GccApprovedByCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'signature',
        'gcc_id',
        'customer_id',
        'customer_site_id',
    ];

    protected $casts = [];
}
