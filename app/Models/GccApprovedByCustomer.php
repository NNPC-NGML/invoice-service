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
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
