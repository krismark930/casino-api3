<?php
namespace App\Http\Controllers\Admin\ThirdParty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Sport;

// error_reporting(E_ALL);
// example of how to use advanced selector features
require(__DIR__.'/simplehtmldom/simple_html_dom.php');
// require(__DIR__.'/curl_http.php');
function GetUrl_HG($url,$header=null,$post=null,$timeout=60,$refe_url=null,$cookie=null,$ip_address=null){
	$server_agent='Mozilla/5.0 (Windows NT 5.2; rv:32.0) Gecko/20100101 Firefox/32.0';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if($header){  //设置header
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if($post){  //启用POST提交
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  //设置POST提交的字符串
	}
	if($refe_url){
		curl_setopt($ch, CURLOPT_REFERER, $refe_url); 
	}
	if($ip_address){
		curl_setopt($ch,CURLOPT_INTERFACE,$ip_address);  //绑定IP
	}
	if($cookie){
		curl_setopt ($ch, CURLOPT_COOKIE, $cookie);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);  //超时60秒
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	curl_setopt($ch, CURLOPT_USERAGENT, $server_agent);  //设置浏览器类型，含代理号
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 2);
	$html = curl_exec($ch);
	// header('Content-Type: image/jpeg');
	header("Access-Control-Allow-Origin: *");
	curl_close($ch);
	flush();
	return $html;
}

class SportScoreResultController extends Controller {

	public function getScoreResult(Request $request) {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {

            $rules = [
                'uid' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $request_data = $request->all();

            $uid = $request_data["uid"];
            $date = $request_data["date"];
			$game_type = $request_data["game_type"];
			if ($date == "Today") {
				$list_date = date('Y-m-d');
			} else {
				$list_date = date('Y-m-d', strtotime('-1 day'));
			}
			// $list_date = "2023-05-31";

			$url = "http://125.252.69.119/app/member/account/result/result.php?game_type=$game_type&list_date=$list_date&uid=$uid&langx=zh-cn";

			//网址,post内容,超时,来路网址,cookie,header,ip地址
			$str =GetUrl_HG($url);

			$index = strpos($str,"doctype html")-2;

			
			$str = substr($str,$index);

			$html = str_get_html($str);

			if(!$html){
				echo "html解析错误";
				die();
			}

			$isTeam = 0;
			$teams = [];
			
			$gid = '';
			$team_h = 0;
			$team_c = 0;
			$league_name = "";
			$league_id = 0;
			$num_h = 0;
			$num_c = 0;
			$GMH = 0;
			$HGMH = 0;
			$GMC = 0;
			$HGMC = 0;
			$datetime = 0;
			$date = 0;
			$time = 0;			
			$count = 0;

			foreach($html->find('tr') as $ul) {
				$class =  $ul->getAttribute("class");
				if($class == "acc_results_league"){
					if($isTeam > 1 ){
						// return $isTeam;
						$teams[$gid] = array(
							"gid" => $gid,
							"team_h" => $team_h,
							"team_c" => $team_c,
							"league_name" => $league_name,
							"league_id" => $league_id,
							"num_h" => $num_h,
							"num_c" => $num_c,
							"GMH" => $GMH,
							"HGMH" => $HGMH,
							"GMC" => $GMC,
							"HGMC" => $HGMC,
							"datetime" => $datetime,
							"date" => $date,
							"time" => $time,
						);
					}
					$td = $ul->find('td');
					$ids = $td[0]->getAttribute("id");
					$arr = explode('_',$ids);
					$league_id = $arr[2]; //联盟id
					$span = $ul->find('span');
					$league_name = $span[0]->innertext;
					$isTeam = 1;
				}
				if($class == "acc_result_tr_top"){
					$ids = $ul->getAttribute("id");
					$arr = explode('_',$ids);
					$gid = $arr[2]; //gid
			
					$td= $ul->find('.acc_result_time');
					$times =$td[0]->innertext;

					$times_arr = explode('<br>',$times);

					$datetime = $times;
					$date = $times_arr[0];
					$time = $times_arr[1];
			
					$team_el = $ul->find('.acc_result_team');
					if(count($team_el)) {
						$team_h =$team_el[0]->innertext;
						$team_h = trim(str_replace( '&nbsp;', ' ',$team_h));
					}
					$spans = $ul->find('span');
					if(count($spans) >= 2) {
						$GMH = $spans[0]->innertext;
						$HGMH = $spans[1]->innertext;
					}
					$isTeam ++;
					$count = $count +1;
					// return $isTeam;
				}
					
				if($class == "acc_result_tr_other"){

					// $team_el = $ul->find('.acc_result_team');
					// $team_c =$team_el[0]->innertext;
					// $team_c  = trim(strtr('&nbsp;', ' ',$team_c));
					// $spans = $ul->find('span');
					// $GMC = $spans[0]->innertext;
					// $HGMC = $spans[1]->innertext;
					// $isTeam ++;


					$ids = $ul->getAttribute("id");
					$arr = explode('_',$ids);
					if(count($arr)  ==3 ){
						$team_el = $ul->find('.acc_result_team');
						$team_h =$team_el[0]->innertext;
						$team_h = trim(str_replace( '&nbsp;', ' ',$team_h));
						$spans = $ul->find('span');
						$GMH = $spans[0]->innertext;
						$HGMH = $spans[1]->innertext;
					}
					if(count($arr)  ==4 ){
						$team_el = $ul->find('.acc_result_team');
						$team_c =$team_el[0]->innertext;
						$team_c  = trim(strtr('&nbsp;', ' ',$team_c));
						$spans = $ul->find('span');
						$GMC = $spans[0]->innertext;
						$HGMC = $spans[1]->innertext;
					}
					$isTeam ++;
				}
					
				if($class == "acc_result_tr_topBL"){
			
					$ids = $ul->getAttribute("id");
					$arr = explode('_',$ids);
					$gid = $arr[2]; //gid
			
					$td= $ul->find('.acc_result_time');
					$times =$td[0]->innertext;

					$times_arr = explode('<br>',$times);

					$datetime = $times;
					$date = $times_arr[0];
					$time = $times_arr[1];
					$count = $count +1;
					$isTeam ++;
				}
					
					
				if($class == "acc_result_tr_otherBL"){
					$ids = $ul->getAttribute("id");
					$arr = explode('_',$ids);
					if(count($arr)  ==3 ){
						$team_el = $ul->find('.acc_result_team');
						$team_h =$team_el[0]->innertext;
						$team_h = trim(str_replace( '&nbsp;', ' ',$team_h));
						$spans = $ul->find('span');
						$GMH = $spans[0]->innertext;
						$HGMH = $spans[1]->innertext;
					}
					if(count($arr)  ==4 ){
						$team_el = $ul->find('.acc_result_team');
						$team_c =$team_el[0]->innertext;
						$team_c  = trim(strtr('&nbsp;', ' ',$team_c));
						$spans = $ul->find('span');
						$GMC = $spans[0]->innertext;
						$HGMC = $spans[1]->innertext;
					}
					$isTeam ++;
				}

                if ($GMH == Score1 or $GMC == Score1){
                    $GMH = -1;
                    $GMC = -1;
                }

                if ($HGMH==Score1 or $HGMC==Score1){
                    $HGMH = -1;
                    $HGMC = -1;
                }

                if($GMH == Score2 or $GMC == Score2){
                    $GMH = -2;
                    $GMC = -2;
                }

                if($HGMH == Score2 or $HGMC == Score2){
                    $HGMH = -2;
                    $HGMC = -2;
                }

                if ($GMH == Score3 or $GMC == Score3){
                    $GMH = -3;
                    $GMC = -3;
                }

                if ($HGMH == Score3 or $HGMC == Score3){
                    $HGMH = -3;
                    $HGMC = -3;
                }

                if ($GMH==Score4 or $GMC==Score4){
                    $GMH = -4;
                    $GMC = -4;
                }

                if ($HGMH==Score4 or $HGMC==Score4){
                    $HGMH = -4;
                    $HGMC = -4;
                }

                if ($GMH==Score5 or $GMC==Score5){
                    $GMH = -5;
                    $GMC = -5;
                }

                if ($HGMH==Score5 or $HGMC==Score5){
                    $HGMH = -5;
                    $HGMC = -5;
                }

                if ($GMH==Score6 or $GMC==Score6){
                    $GMH = -6;
                    $GMC = -6;
                }

                if ($HGMH==Score6 or $HGMC==Score6){
                    $HGMH = -6;
                    $HGMC = -6;
                }

                if ($GMH==Score7 or $GMC==Score7){
                    $GMH = -7;
                    $GMC = -7;
                }

                if ($HGMH==Score7 or $HGMC==Score7){
                    $HGMH = -7;
                    $HGMC = -7;
                }

                if ($GMH==Score8 or $GMC==Score8){
                    $GMH = -8;
                    $GMC = -8;
                }

                if ($HGMH==Score8 or $HGMC==Score8){
                    $HGMH = -8;
                    $HGMC = -8;
                }

                if ($GMH==Score9 or $GMC==Score9){
                    $GMH = -9;
                    $GMC = -9;
                }

                if ($HGMH==Score9 or $HGMC==Score9){
                    $HGMH = -9;
                    $HGMC = -9;
                }

                if ($GMH==Score10 or $GMC==Score10){
                    $GMH = -10;
                    $GMC = -10;
                }

                if ($HGMH==Score10 or $HGMC==Score10){
                    $HGMH = -10;
                    $HGMC = -10;
                }

                if ($GMH==Score11 or $GMC==Score11){
                    $GMH = -11;
                    $GMC = -11;
                }

                if ($HGMH==Score11 or $HGMC==Score11){
                    $HGMH = -11;
                    $HGMC = -11;
                }

                if ($GMH==Score12 or $GMC==Score12){
                    $GMH = -12;
                    $GMC = -12;
                }

                if ($HGMH==Score12 or $HGMC==Score12){
                    $HGMH = -12;
                    $HGMC = -12;
                }

                if ($GMH==Score13 or $GMC==Score13){
                    $GMH = -13;
                    $GMC = -13;
                }

                if ($HGMH==Score13 or $HGMC==Score13){
                    $HGMH = -13;
                    $HGMC = -13;
                }

                if ($GMH == Score14 or $GMC == Score14) {
                	$GMH = -14;
                	$GMC = -14;
                }

                if ($HGMH == Score14 or $HGMC == Score14){
                    $HGMH = -14;
                    $HGMC = -14;
                }

                if ($GMH == Score15 or $GMC == Score15) {
                	$GMH = -14;
                	$GMC = -14;
                }

                if ($HGMH == Score15 or $HGMC == Score15){
                    $HGMH = -14;
                    $HGMC = -14;
                }

                if ($GMH==Score19 or $GMC==Score19){
                    $GMH = -19;
                    $GMC = -19;
                }

                if ($HGMH==Score19 or $HGMC==Score19){
                    $HGMH = -19;
                    $HGMC = -19;
                }

                $match_sports = Sport::where("Type", $game_type)
                	->where("MID", (int)$gid)
                	->where("M_Date", $list_date)
                	->first();

                if (isset($match_sports)) {

                    if ($match_sports['MB_Inball'] == "") {
                    	if ($game_type == "FT") {
	                        Sport::where("Type", "FT")
	                            ->where("M_Date", $list_date)
	                            ->where("MID", (int)$gid)
	                            ->update([
	                                "MB_Inball" => $GMH,
	                                "TG_Inball" => $GMC,
	                                "MB_Inball_HR" => $HGMH,
	                                "TG_Inball_HR" => $HGMC
	                            ]);                    		
                    	} else {
                    		Sport::where("Type", "BK")
	                            ->where("M_Date", $list_date)
	                            ->where("ECID", (int)$match_sports["ECID"])
	                            ->where('MB_Team', 'not like', "%第一节%")
	                            ->where('MB_Team', 'not like', "%第二节%")
	                            ->where('MB_Team', 'not like', "%第三节%")
	                            ->where('MB_Team', 'not like', "%第四节%")
	                            ->where('MB_Team', 'not like', "%上半场%")
	                            ->where('MB_Team', 'not like', "%下半场%")
	                            ->where('MB_Team', 'not like', "%加时%")
	                            ->update([
	                                "MB_Inball" => $GMH,
	                                "TG_Inball" => $GMC,
	                                "MB_Inball_HR" => $match_sports["MB_Inball_HR"],
	                                "TG_Inball_HR" => $match_sports["TG_Inball_HR"],
	                            ]);                    		
                    	}
                    } elseif( $GMH < 0 || $GMC < 0 ) {
                    	if ($game_type == "FT") {
	                        Sport::where("Type", "FT")
	                            ->where("M_Date", $list_date)
	                            ->where("MID", (int)$gid)
	                            ->update([
	                                "MB_Inball" => $GMH,
	                                "TG_Inball" => $GMC,
	                                "MB_Inball_HR" => $HGMH,
	                                "TG_Inball_HR" => $HGMC,
	                                "Cancel" => 1
	                            ]);      		
                    	} else {
                    		Sport::where("Type", "BK")
	                            ->where("M_Date", $list_date)
	                            ->where("ECID", (int)$match_sports["ECID"])
	                            ->where('MB_Team', 'not like', "%第一节%")
	                            ->where('MB_Team', 'not like', "%第二节%")
	                            ->where('MB_Team', 'not like', "%第三节%")
	                            ->where('MB_Team', 'not like', "%第四节%")
	                            ->where('MB_Team', 'not like', "%上半场%")
	                            ->where('MB_Team', 'not like', "%下半场%")
	                            ->where('MB_Team', 'not like', "%加时%")
	                            ->update([
	                                "MB_Inball" => $GMH,
	                                "TG_Inball" => $GMC,
	                                "MB_Inball_HR" => $match_sports["MB_Inball_HR"],
	                                "TG_Inball_HR" => $match_sports["TG_Inball_HR"],
	                                "Cancel" => 1
	                            ]);
                    	}
                    } else {
                    	if ($game_type == "FT") {

	                        $a= $match_sports['MB_Inball'].$match_sports['TG_Inball'].$match_sports['MB_Inball_HR'].$match_sports['TG_Inball_HR'];

	                        $b= trim($GMH).trim($GMC).trim($HGMH).trim($HGMC);

	                        // return strcmp($a, $b);

	                        if($a != $b) {
	                            Sport::where("Type", "FT")
	                                ->where("GetScore", 1)
	                                ->where("M_Date", $list_date)
	                                ->where("MID", (int)$gid)
	                                ->update([
	                                    "MB_Inball" => $GMH,
	                                    "TG_Inball" => $GMC,
	                                    "MB_Inball_HR" => $HGMH,
	                                    "TG_Inball_HR" => $HGMC,
	                                    // "Checked" => 1
	                                ]);
	                        }else{
	                            Sport::where("Type", "FT")
	                                ->where("GetScore", 1)
	                                ->where("M_Date", $list_date)
	                                ->where("MID", (int)$gid)
	                                ->update([
	                                    "MB_Inball" => $GMH,
	                                    "TG_Inball" => $GMC,
	                                    "MB_Inball_HR" => $HGMH,
	                                    "TG_Inball_HR" => $HGMC
	                                ]);
	                        }

                    	} else {
	                        Sport::where("Type", "BK")
	                            ->where("M_Date", $list_date)
	                            ->where("ECID", (int)$match_sports["ECID"])
	                            ->where('MB_Team', 'not like', "%第一节%")
	                            ->where('MB_Team', 'not like', "%第二节%")
	                            ->where('MB_Team', 'not like', "%第三节%")
	                            ->where('MB_Team', 'not like', "%第四节%")
	                            ->where('MB_Team', 'not like', "%上半场%")
	                            ->where('MB_Team', 'not like', "%下半场%")
	                            ->where('MB_Team', 'not like', "%加时%")
	                            ->update([
	                                "MB_Inball" => $GMH,
	                                "TG_Inball" => $GMC,
	                            ]);

	                        $a= $match_sports['MB_Inball'].$match_sports['TG_Inball'];

	                        $b= trim($GMH).trim($GMC);

	                        if($a != $b) {
	                            Sport::where("Type", "BK")
	                                ->where("GetScore", 1)
	                                ->where("M_Date", $list_date)
	                                ->where("ECID", (int)$match_sports["ECID"])
		                            ->where('MB_Team', 'not like', "%第一节%")
		                            ->where('MB_Team', 'not like', "%第二节%")
		                            ->where('MB_Team', 'not like', "%第三节%")
		                            ->where('MB_Team', 'not like', "%第四节%")
		                            ->where('MB_Team', 'not like', "%上半场%")
		                            ->where('MB_Team', 'not like', "%下半场%")
		                            ->where('MB_Team', 'not like', "%加时%")
	                                ->update([
	                                    "MB_Inball" => $GMH,
	                                    "TG_Inball" => $GMC,
	                                    // "Checked" => 1
	                                ]);
	                        }else{
	                            Sport::where("Type", "BK")
	                                ->where("GetScore", 1)
	                                ->where("M_Date", $list_date)
	                                ->where("ECID", (int)$match_sports["ECID"])
		                            ->where('MB_Team', 'not like', "%第一节%")
		                            ->where('MB_Team', 'not like', "%第二节%")
		                            ->where('MB_Team', 'not like', "%第三节%")
		                            ->where('MB_Team', 'not like', "%第四节%")
		                            ->where('MB_Team', 'not like', "%上半场%")
		                            ->where('MB_Team', 'not like', "%下半场%")
		                            ->where('MB_Team', 'not like', "%加时%")
	                                ->update([
	                                    "MB_Inball" => $GMH,
	                                    "TG_Inball" => $GMC,
	                                ]);
	                        }

                    	}
                    }

                }
				
			}

			$result = array("total_count"=>$count,"results_data"=>$teams);

			$response['data'] = $result;
            $response['message'] = 'Other Score Data fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
	}
}
