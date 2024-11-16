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
        'total_quantity_of_gas',
        'department',
        'gcc_created_by_id',
        'invoice_advice_created_by_id',
        'from_date',
        'to_date',
    ];

    protected $casts = [
        'with_vat' => 'boolean',
        'date' => 'datetime',
        'status' => 'integer',
        'total_quantity_of_gas' => 'float',
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function invoice_advice_list_items()
    {
        return $this->hasMany(InvoiceAdviceListItem::class, 'invoice_advice_id');
    }

    public function customer()
    {
        return $this->belongsTo(\Skillz\Nnpcreusable\Models\Customer::class, 'customer_id');
    }

    public function customer_site()
    {
        return $this->belongsTo(\Skillz\Nnpcreusable\Models\CustomerSite::class, 'customer_site_id');
    }

    public function gcc_created_by()
    {
        return $this->belongsTo(\Skillz\Nnpcreusable\Models\User::class, 'gcc_created_by_id');
    }

    public function invoice_advice_created_by()
    {
        return $this->belongsTo(\Skillz\Nnpcreusable\Models\User::class, 'invoice_advice_created_by_id');
    }
}
