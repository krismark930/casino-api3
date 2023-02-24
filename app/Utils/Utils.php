<?php

namespace App\Utils;

use App\Models\Web\UpdateLog;

class Utils {
    static function ProcessUpdate($username){
		global $dbname;
		do{
			$flag=1;
			$MD5String=md5($username.date("YmdHis",time()+12*3600));
			$DateTime=time();
			//$sql = "insert into web_update_log set MD5String='$MD5String',DateTime='$DateTime'";
            $data = [
                "MD5String" => $MD5String,
                "DateTime" => $DateTime,
            ];
            $updateLog = new UpdateLog;
            if ($updateLog->create($data)){
            }else{
                $flag=0;
            }
			if($flag==0) sleep(1);  //暂停1秒
		}while($flag==0);
	}
}
