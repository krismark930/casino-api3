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
    ];

    protected $table = 'web_bank_data';

    public $timestamps = false;

}
