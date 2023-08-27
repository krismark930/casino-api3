<?php

namespace App\Http\Controllers\Admin\DataManipulation;

use App\Http\Controllers\Controller;
use App\Models\GUser;
use App\Models\User;
use App\Models\Web\Report;
use App\Models\Web\System;
use Exception;
use Illuminate\Http\Request;

class AdminParlayController extends Controller
{
    public function show_voucher($line, $id)
    {
        $data = System::first();
        // Assigning values to variables
        $ouid = $data->OUID;
        $dtid = $data->DTID;
        $pmid = $data->PMID;

        // Switch case
        switch ($line) {
            case 1:
            case 2:
            case 3:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 4:
            case 5:
            case 6:
            case 7:
                $show_voucher = 'DT' . ($id + $dtid);
                break;
            case 8:
                $show_voucher = 'PM' . ($id + $pmid);
                break;
            case 9:
            case 10:
            case 11:
            case 12:
            case 13:
            case 15:
            case 19:
            case 20:
            case 21:
            case 31:
                $show_voucher = 'OU' . ($id + $ouid);
                break;
            case 16:
                $show_voucher = 'CS' . ($id + $ouid);
                break;
            default:
                $show_voucher = ''; // default value
        }

        return $show_voucher;
    }

    public function getItems(Request $request)
    {
        $page = $request['page'];
        // $id = $request['id'];
        // $type = $request['type'];
        $date = date('Y-m-d');

        try {
            $rows = Report::where('M_Date', $date)
                ->where('LineType', '8');
            $totalCount = $rows->count();
            $rows = $rows->orderBy('BetTime')
                ->offset($page * 20 - 20)->limit(20)->get();

            $data = array();
            foreach ($rows as $row) {
                $Title = ['', '足球', '篮球', '棒球', '网球', '排球', '其它', '指数'][$row['Active'] % 10];
                $Rep_HK = '香港盘';
                $Rep_Malay = '马来盘';
                $Rep_Indo = '印尼盘';
                $Rep_Euro = '欧洲盘';
                switch ($row['OddsType']) {
                    case 'H':
                        $Odds = '<BR><font color =green>' . $Rep_HK . '</font>';
                        break;
                    case 'M':
                        $Odds = '<BR><font color =green>' . $Rep_Malay . '</font>';
                        break;
                    case 'I':
                        $Odds = '<BR><font color =green>' . $Rep_Indo . '</font>';
                        break;
                    case 'E':
                        $Odds = '<BR><font color =green>' . $Rep_Euro . '</font>';
                        break;
                    case '':
                        $Odds = '';
                        break;
                }
                $ztIndex = intval(-$row['Confirmed']);
                if ($ztIndex > 32) {
                    $ztIndex -= 20;
                }

                $zt = [
                    '[注单确认]',
                    '[取消]',
                    '[赛事腰斩]',
                    '[赛事改期]',
                    '[赛事延期]',
                    '[赛事延赛]',
                    '[赛事取消]',
                    '[赛事无PK加时]',
                    '[球员弃权]',
                    '[队名错误]',
                    '[主客场错误]',
                    '[先发投手更换]',
                    '[选手更换]',
                    '[联赛名称错误]',
                    '[盘口错误]',
                    '[提前开赛]',
                    '[比分错误]',
                    '[未接受注单]',
                    '[进球取消]',
                    '[红卡取消]',
                    '[非正常投注]',
                    '[赔率错误]',
                ][$ztIndex];
                array_push($data, array(
                    'id' => $row['ID'],
                    'gid' => $row['MID'],
                    'payType' => $row['Pay_Type'],
                    'result' => $row['M_Result'],
                    'user' => $row['M_Name'],
                    //------------------------------
                    'betTimes' => $row['BetTime'],
                    'mName' => $row['M_Name'] . '<br/><font color="#cc0000">' . $row['OpenType'] . '&nbsp;&nbsp;' . $row['TurnRate'] . '</font>',
                    'betType' => $Title . $row['BetType'] . $Odds . '<br/><font color="#0000cc">' . $this->show_voucher($row['LineType'], $row['ID']) . '</font>',
                    'mid' => $row['MID'],
                    'middle' => $row['Middle'],
                    'betScore' => $row['BetScore'],
                    'mResult' => $row['Cancel'] ? '<font color="red">' . $zt . '</font>' : number_format(floatval($row['M_Result']), 1),
                    'cancel' => $row['Cancel'],
                ));
            }
            return array(
                'data' => $data,
                'totalCount' => $totalCount,
            );
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getFunctions(Request $request)
    {
        return [
            '[取消]',
            '[赛事腰斩]',
            '[赛事改期]',
            '[赛事延期]',
            '[赛事延赛]',
            '[赛事取消]',
            '[赛事无PK加时]',
            '[球员弃权]',
            '[队名错误]',
            '[主客场错误]',
            '[先发投手更换]',
            '[选手更换]',
            '[联赛名称错误]',
            '[盘口错误]',
            '[提前开赛]',
            '[比分错误]',
            '[未接受注单]',
            '[进球取消]',
            '[红卡取消]',
            '[非正常投注]',
            '[赔率错误]',
        ];
    }

    public function MoneyToSsc($t_user)
    {
        $rowuser = User::where('UserName', $t_user)->get();
        if (count($rowuser)) {
            $rowuser = $rowuser[0];
            if (!empty($rowuser['UserName'])) {
                GUser::where('g_name', $rowuser['UserName'])
                    ->update(array(
                        'g_money' => $rowuser['Credit'],
                        'g_money_yes' => $rowuser['Money'],
                    ));
            }
        }
    }

    public function cancelEvent(Request $request)
    {
        $id = $request['id'];
        $gid = $request['gid'];
        $confirmed = $request['confirmed'];

        try {
            $rows = Report::where('MID', $gid)
                ->where('ID', $id)
                ->where('Pay_Type', 1)
                ->get();
            foreach ($rows as $row) {
                $username = $row['M_Name'];
                $betscore = $row['BetScore'];
                $m_result = $row['M_Result'];
                if ($row['Pay_Type']) {
                    if ($m_result == '') {
                        try {
                            User::where('UserName', $username)
                                ->where('Pay_Type', 1)
                                ->increment('Money', $betscore);
                            User::where('UserName', $username)
                                ->where('Pay_Type', 1)
                                ->increment('withdrawal_condition', $betscore);
                            $this->MoneyToSsc($username);
                        } catch (Exception $e) {
                            return response()->json('操作失败11!', 500);
                        }
                    } else {
                        try {
                            User::where('UserName', $username)
                                ->where('Pay_Type', 1)
                                ->decrement('Money', $m_result);
                            User::where('UserName', $username)
                                ->where('Pay_Type', 1)
                                ->increment('withdrawal_condition', $betscore);
                            $this->MoneyToSsc($username);
                        } catch (Exception $e) {
                            return response()->json('操作失败22!', 500);
                        }
                    }
                }
            }
            Report::where('id', $id)
                ->update(array(
                    'VGOLD' => '0',
                    'M_Result' => '0',
                    'A_Result' => '0',
                    'B_Result' => '0',
                    'C_Result' => '0',
                    'D_Result' => '0',
                    'T_Result' => '0',
                    'Cancel' => '1',
                    'Confirmed' => $confirmed,
                    'Danger' => '0',
                    'Checked' => '1',
                ));
            return;
        } catch (Exception $e) {
            return response()->json('操作失败!', 500);
        }
    }

    public function resumeEvent(Request $request)
    {
        $id = $request['id'];
        $gid = $request['gid'];

        try {
            $rows = Report::where('MID', $gid)
                ->where('ID', $id)
                ->where('Pay_Type', 1)
                ->get();
            foreach ($rows as $row) {
                $username = $row['M_Name'];
                $betscore = $row['BetScore'];
                $m_result = $row['M_Result'];
                if ($row['Pay_Type']) {
                    if ($row['Checked'] == 1) {
                        try {
                            $cash = $betscore + $m_result;
                            User::where('UserName', $username)
                                ->where('Pay_Type', 1)
                                ->decrement('Money', $cash);
                            User::where('UserName', $username)
                                ->where('Pay_Type', 1)
                                ->decrement('withdrawal_condition', $betscore);
                            $this->MoneyToSsc($username);
                        } catch (Exception $e) {
                            return response()->json('操作失败1!', 500);
                        }
                    }
                }
            }
            Report::where('id', $id)
                ->update(array(
                    'VGOLD' => '',
                    'M_Result' => '',
                    'A_Result' => '',
                    'B_Result' => '',
                    'C_Result' => '',
                    'D_Result' => '',
                    'T_Result' => '',
                    'Cancel' => '0',
                    'Confirmed' => '0',
                    'Danger' => '0',
                    'Checked' => '0',
                ));
            return;
        } catch (Exception $e) {
            return response()->json('操作失败!', 500);
        }
    }

    public function modifyEvent(Request $request)
    {
        $id = $request['id'];
        $mid = $request['mid'];

        try {
            Report::where('ID', $id)->update(array(
                'MID' => $mid,
            ));
            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

}
