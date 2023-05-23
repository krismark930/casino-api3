<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebReportZr extends Model
{
    use HasFactory;

    protected $fillable = [
        "billNo",
        "UserName",
        "playerName",
        "Type",
        "gameType",
        "gameCode",
        "netAmount",
        "betTime",
        "betAmount",
        "validBetAmount",
        "playType",
        "tableCode",
        "loginIP",
        "recalcuTime",
        "round",
        "platformType",
        "VendorId",
        "Checked",
        "isFS",
    ];

    protected $table = "web_report_zr";
    public $timestamps = false;
}
