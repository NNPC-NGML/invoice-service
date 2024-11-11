<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Skillz\Nnpcreusable\Models\Customer;
use Skillz\Nnpcreusable\Models\CustomerSite;

class DailyVolume extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'customer_site_id', 'volume', 'remark'];

    protected $casts = [
        'customer_id' => 'integer',
        'customer_site_id' => 'integer',
        'volume' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customer_site()
    {
        return $this->belongsTo(CustomerSite::class);
    }
}