<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Report;
use App\Utils\Utils;
use App\Models\Sport;

class AdminSearchBettingController extends Controller
{
    //
    public function getItems(Request $request)
    {
        $m_date = $request['m_date'] ?? date('Y-m-d');
        // $mids = Report::select('MID')->where('M_Date', $m_date)->get();

        $data = array();

        $mids = Report::select()->where('M_Date', $m_date)->orderBy('BetTime', "desc")->get();
        $items = Sport::select('MID')->get();

        foreach($mids as $row){



            if($row['Cancel'] == 1){
                $operate = '<font color=red><b>恢复</b></font></a>';
            }else {
                $operate = '<font color=blue><b>正常</b></font>';
            }


            //  state
            if($row['Active'] == 0){
                $state = '结算';
            }else if($row['Active'] == 1){
                $state = '<font color=red>未结算</font>';
            }

            $temp = array(
                'id' => $row->id,
                'userName' => $row['M_Name'],
                'minutes' => $row->id,
                'bettingTime' => $row['BetTime'],
                'startingTime' => $row['BetTime'],
                'gameType' => $row['BetType'],
                'content' => $row['Middle'],
                'state' => $state,
                'betAmount' => $row['BetScore'],
                'winableAmount' => $row['Gwin'],
                'memberResult' => '0',
                'betSlip' => $operate,
                'function' => 'function',
            );
            array_push($data, $temp);
        }
        return $data;
    }

    public function getFunctionItems() {
        $scors = Utils::Scores;
        $scors = array_splice($scors, 20, 23);
        $data = array();

        foreach($scors as $row) {
            $temp = array(
                'label' => $row,
                'value' => $row,
            );
            array_push($data, $temp);
        }
        return $data;
    }
}