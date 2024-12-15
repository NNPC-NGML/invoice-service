<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GccApprovedByAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_advice_id',
        'gcc_id',
        'customer_id',
        'customer_site_id',
    ];

    protected $casts = [];

    // Optional: Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
