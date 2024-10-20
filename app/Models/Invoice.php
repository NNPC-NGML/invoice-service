<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_advice_id',
        'consumed_volume_amount_in_naira',
        'consumed_volume_amount_in_dollar',
        'dollar_to_naira_convertion_rate',
        'vat_amount',
        'total_volume_paid_for',
        'status'
    ];

    protected $casts = [
        'invoice_advice_id' => 'integer',
        'status' => 'integer',
    ];
}
