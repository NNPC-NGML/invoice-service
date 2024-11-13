<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAdvice extends Model
{
    use HasFactory;

    protected $fillable = [
        'with_vat',
        'customer_id',
        'customer_site_id',
        'capex_recovery_amount',
        'date',
        'status',
        'department',
        'gcc_created_by',
        'invoice_advice_created_by'
    ];

    protected $casts = [
        'with_vat' => 'boolean',
        'date' => 'datetime',
        'status' => 'integer'
    ];
}
