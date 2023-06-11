<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebPaymentData extends Model
{
    use HasFactory;

    protected $fillable = [        
        "Address",
        "Business",
        "Keys",
        "TerminalID",
        "FixedGold",
        "VIP",
        "WAP",
        "Limit1",
        "Limit2",
        "Switch",
        "Music",
        "Sort",
    ];
    protected $table = "web_payment_data";
    public $timestamps = false;
}
