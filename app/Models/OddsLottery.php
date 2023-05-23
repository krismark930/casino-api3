<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OddsLottery extends Model
{
    use HasFactory;

    protected $table = "odds_lottery";

    public $timestamps = false;
}
