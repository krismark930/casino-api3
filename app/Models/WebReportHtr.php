<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebReportHtr extends Model
{
    use HasFactory;
    protected $table = "web_report_htr";

    protected $fillable = [
        "tradeNo",
        "UserName",
        "playerName",
        "Type",
        "platformType",
        "sceneId",
        "SceneStartTime",
        "SceneEndTime",
        "Roomid",
        "Roombet",
        "Cost",
        "Earn",
        "Jackpotcomm",
        "transferAmount",
        "previousAmount",
        "currentAmount",
        "IP",
        "VendorId",
        "Checked",
    ];

    public $timestamps = false;
}
