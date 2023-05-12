<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotterySchedule extends Model
{
    use HasFactory;

    protected $table = "lottery_schedule";

    public $timestamps = false;
}
