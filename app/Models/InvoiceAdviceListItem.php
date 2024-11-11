<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAdviceListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_site_id',
        'invoice_advice_id',
        'daily_volume_id',
        'volume',
        'inlet',
        'outlet',
        'take_or_pay_value',
        'allocation',
        'daily_target',
        'nomination',
        'date',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'integer'
    ];
}
