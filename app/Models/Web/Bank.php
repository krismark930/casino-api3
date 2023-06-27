<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model {

    protected $fillable = [
        "bankname",
        "alias",
        "bankno",
        "bankaddress",
        "vip",
        "min_amount",
        "max_amount",
    ];

    protected $table = 'web_bank_data';

    public $timestamps = false;

}
