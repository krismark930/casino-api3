<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLottery extends Model
{
    use HasFactory;
    protected $table = "order_lottery";
    public $timestamps = false;
}
