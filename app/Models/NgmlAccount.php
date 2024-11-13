<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgmlAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'bank_address',
        'account_name',
        'account_number',
        'sort_code',
        'tin',
    ];
}
