<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OddsLotteryNormal extends Model
{
    use HasFactory;

    protected $table = "odds_lottery_normal";

    public $timestamps = false;
}
