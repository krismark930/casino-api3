<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class MatchSports extends Model {
    protected $fillable = [
        'MID',
        'Type',
        'M_Date',
        'M_Time',
        'M_Start',
        'MB_Team',
        'TG_Team',
        'MB_Team_tw',
        'TG_Team_tw',
        'MB_Team_en',
        'TG_Team_en',
        'M_League',
        'M_League_tw',
        'M_League_en',
    ];
    protected $table = 'match_sports';
    public $timestamps = false;
}
