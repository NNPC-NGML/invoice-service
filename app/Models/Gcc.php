<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gcc extends Model
{
    use HasFactory;
    const GCCCREATED = 11;
    const GCCAPPROVEDBYADMIN = 10;
    const GCCAPPROVEDBYCUSTOMER = 9;
    const INVOICEADVICECREATED = 8;
    const INVOICEADVICECHECKEDBY = 7;
    const INVOICEADVICECONFIRMEDBY = 6;
    const INVOICEADVICEAPPROVEDBY = 5;
    const INVOICECREATED = 4;
    const INVOICEAPPROVEDBY = 3;
    const CUSTOMERINVOICEPAYMENT = 2;
    const PAYMENTCONFIRMED = 1;


    protected $fillable = [
        "with_vat",
        "customer_id",
        "customer_site_id",
        "capex_recovery_amount",
        "gcc_date",
        "department_id",
        "gcc_created_by",
        "letter_id",
        "status",
    ];

    public function invoiceAdvice()
    {
        return $this->hasOne(InvoiceAdvice::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function listItems()
    {
        return $this->hasMany(InvoiceAdviceListItem::class);
    }
}
