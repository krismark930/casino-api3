<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class MoneyLog extends Model {

    protected $fillable = [
        'user_id',
        'order_num',
        'about',
        'update_time',
        'type',
        'order_value',
        'assets',
        'balance'];

    protected $table = 'money_log';
    public $timestamps = false;
}
