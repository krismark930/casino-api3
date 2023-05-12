<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryUserConfig extends Model
{
    use HasFactory;
    protected $table = "lottery_user_config";
    public $timestamps = false;
}
