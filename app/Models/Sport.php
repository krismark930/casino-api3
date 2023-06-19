<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'MID',
        "Type",
        "MB_Team",
        "TG_Team",
        "MB_Team_tw",
        "TG_Team_tw",
        "MB_Team_en",
        "TG_Team_en",
        "M_Date",
        "M_Time",
        "M_Start",
        "M_League",
        "M_League_tw",
        "M_League_en",
        "M_Type",
        'ShowTypeR',
        "MB_Win_Rate",
        "TG_Win_Rate",
        "M_Flat_Rate",
        "M_LetB",
        "MB_LetB_Rate",
        "TG_LetB_Rate",
        "MB_Dime",
        "TG_Dime",
        "MB_Dime_Rate",
        "TG_Dime_Rate",
        "ShowTypeHR",
        "MB_Win_Rate_H",
        "TG_Win_Rate_H",
        "M_Flat_Rate_H",
        "M_LetB_H",
        "MB_LetB_Rate_H",
        "TG_LetB_Rate_H",
        "MB_Dime_H",
        "TG_Dime_H",
        "MB_Dime_Rate_H",
        "TG_Dime_Rate_H",
        "ShowTypeRB",
        "ShowTypeP",
        "M_P_LetB",
        "MB_P_LetB_Rate",
        "TG_P_LetB_Rate",
        "MB_P_Dime",
        "TG_P_Dime",
        "MB_P_Dime_Rate",
        "TG_P_Dime_Rate",
        "S_P_Single_Rate",
        "S_P_Double_Rate",
        "MB_P_Win_Rate",
        "TG_P_Win_Rate",
        "M_P_Flat_Rate",
        "ShowTypeHP",
        "M_P_LetB_H",
        "MB_P_LetB_Rate_H",
        "TG_P_LetB_Rate_H",
        "MB_P_Dime_H",
        "TG_P_Dime_H",
        "MB_P_Dime_Rate_H",
        "TG_P_Dime_Rate_H",
        "MB_P_Win_Rate_H",
        "TG_P_Win_Rate_H",
        "M_P_Flat_Rate_H",
        'P3_Show',
        "MB1TG0",
        "MB2TG0",
        "MB2TG1",
        "MB3TG0",
        "MB3TG1",
        "MB3TG2",
        "MB4TG0",
        "MB4TG1",
        "MB4TG2",
        "MB4TG3",
        "MB0TG0",
        "MB1TG1",
        "MB2TG2",
        "MB3TG3",
        "MB4TG4",
        "MB0TG1",
        "MB0TG2",
        "MB1TG2",
        "MB0TG3",
        "MB1TG3",
        "MB2TG3",
        "MB0TG4",
        "MB1TG4",
        "MB2TG4",
        "MB3TG4",
        "MB1TG0H",
        "MB2TG0H",
        "MB2TG1H",
        "MB3TG0H",
        "MB3TG1H",
        "MB3TG2H",
        "MB4TG0H",
        "MB4TG1H",
        "MB4TG2H",
        "MB4TG3H",
        "MB0TG0H",
        "MB1TG1H",
        "MB2TG2H",
        "MB3TG3H",
        "MB4TG4H",
        "MB0TG1H",
        "MB0TG2H",
        "MB1TG2H",
        "MB0TG3H",
        "MB1TG3H",
        "MB2TG3H",
        "MB0TG4H",
        "MB1TG4H",
        "MB2TG4H",
        "MB3TG4H",
        "UP5",
        "UP5H",
        "PD_Show",
        "FLAG_CLASS",
        "SCORE_H",
        "SCORE_C",
        "ECID",
        "LID",
        "MB_MID",
        "TG_MID",
        "S_Single_Rate",
        "S_Double_Rate",
        "S_Single_Rate_H",
        "S_Double_Rate_H",
        "Eventid",
        "Hot",
        "Play",
        "S_Show",
        "Retime",
        "M_LetB_RB",
        "MB_LetB_Rate_RB",
        "TG_LetB_Rate_RB",
        "MB_Dime_RB",
        "TG_Dime_RB",
        "MB_Dime_Rate_RB",
        "TG_Dime_Rate_RB",
        "ShowTypeHRB",
        "M_LetB_RB_H",
        "MB_LetB_Rate_RB_H",
        "TG_LetB_Rate_RB_H",
        "MB_Dime_RB_H",
        "TG_Dime_RB_H",
        "MB_Dime_Rate_RB_H",
        "TG_Dime_Rate_RB_H",
        "MB_Ball",
        "TG_Ball",
        "MB_Win_Rate_RB",
        "TG_Win_Rate_RB",
        "M_Flat_Rate_RB",
        "MB_Win_Rate_RB_H",
        "TG_Win_Rate_RB_H",
        "M_Flat_Rate_RB_H",
        "RB_Show",
        "isSub",
        "MB_Card",
        "TG_Card",
        "RETIME_SET",        
        "HDP_OU",
        "CORNER",
        "M_LetB_RB_1",
        "MB_LetB_Rate_RB_1",
        "TG_LetB_Rate_RB_1",
        "MB_Dime_RB_1",
        "TG_Dime_RB_1",
        "MB_Dime_Rate_RB_1",
        "TG_Dime_Rate_RB_1",
        "M_LetB_RB_2",
        "MB_LetB_Rate_RB_2",
        "TG_LetB_Rate_RB_2",
        "MB_Dime_RB_2",
        "TG_Dime_RB_2",
        "MB_Dime_Rate_RB_2",
        "TG_Dime_Rate_RB_2",
        "M_LetB_RB_3",
        "MB_LetB_Rate_RB_3",
        "TG_LetB_Rate_RB_3",
        "MB_Dime_RB_3",
        "TG_Dime_RB_3",
        "MB_Dime_Rate_RB_3",
        "TG_Dime_Rate_RB_3",
        "M_LetB_1",
        "MB_LetB_Rate_1",
        "TG_LetB_Rate_1",
        "MB_Dime_1",
        "TG_Dime_1",
        "MB_Dime_Rate_1",
        "TG_Dime_Rate_1",
        "M_LetB_2",
        "MB_LetB_Rate_2",
        "TG_LetB_Rate_2",
        "MB_Dime_2",
        "TG_Dime_2",
        "MB_Dime_Rate_2",
        "TG_Dime_Rate_2",
        "M_LetB_3",
        "MB_LetB_Rate_3",
        "TG_LetB_Rate_3",
        "MB_Dime_3",
        "TG_Dime_3",
        "MB_Dime_Rate_3",
        "TG_Dime_Rate_3",
        "M_P_LetB_1",
        "MB_P_LetB_Rate_1",
        "TG_P_LetB_Rate_1",
        "MB_P_Dime_1",
        "TG_P_Dime_1",
        "MB_P_Dime_Rate_1",
        "TG_P_Dime_Rate_1",
        "M_P_LetB_2",
        "MB_P_LetB_Rate_2",
        "TG_P_LetB_Rate_2",
        "MB_P_Dime_2",
        "TG_P_Dime_2",
        "MB_P_Dime_Rate_2",
        "TG_P_Dime_Rate_2",
        "M_P_LetB_3",
        "MB_P_LetB_Rate_3",
        "TG_P_LetB_Rate_3",
        "MB_P_Dime_3",
        "TG_P_Dime_3",
        "MB_P_Dime_Rate_3",
        "TG_P_Dime_Rate_3",
        "MB_Points_1",
        "TG_Points_1",
        "MB_Points_Rate_1",
        "TG_Points_Rate_1",
        "MB_Points_2",
        "TG_Points_2",
        "MB_Points_Rate_2",
        "TG_Points_Rate_2",
        "MB_Points_1",
        "TG_Points_1",
        "MB_Points_Rate_1",
        "TG_Points_Rate_1",
        "MB_Points_2",
        "TG_Points_2",
        "MB_Points_Rate_2",
        "TG_Points_Rate_2",
        "MB_P_Points_1",
        "TG_P_Points_1",
        "MB_P_Points_Rate_1",
        "TG_P_Points_Rate_1",
        "MB_P_Points_2",
        "TG_P_Points_2",
        "MB_P_Points_Rate_2",
        "TG_P_Points_Rate_2",
        "MBMB",
        "MBFT",
        "MBTG",
        "FTMB",
        "FTFT",
        "FTTG",
        "TGMB",
        "TGTG",
        "TGFT",
        "S_0_1",
        "S_2_3",
        "S_4_6",
        "S_7UP",
        "T_Show",
        "F_Show",
        "HPD_Show",
        "MB_Inball",
        "TG_Inball",
        "MB_Inball_HR",
        "TG_Inball_HR",
    ];

    protected $table = 'match_sports';

    protected $primaryKey = 'MID';

}
