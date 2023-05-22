<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebReportKy extends Model
{
    use HasFactory;

    protected $fillable = [
        'GameID',
        'Accounts',
        'ServerID',
        'KindID',
        'TableID',
        'ChairID',
        'UserCount',
        'CellScore',
        'AllBet',
        'Profit',
        'Revenue',
        'GameStartTime',
        'GameEndTime',
        'CardValue',
        'ChannelID',
        'LineCode',
    ];

    protected $table = "web_report_ky";
    
    public $timestamps = false;
}
