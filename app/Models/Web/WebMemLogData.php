<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class WebMemLogData extends Model {

    protected $fillable = [
        'UserName',
        'Logintime',
        'ConText',
        'Loginip',
        'Url',
    ];
    protected $table = 'web_mem_log_data';
    public $timestamps = false;
}
