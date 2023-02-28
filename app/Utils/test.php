function handleBBIN(Request $request, User $user, SysConfig $sysConfig ) {
        $BBIN_username = $user->BBIN_User;
        $BBIN_password = $user->BBIN_Pass;
        $username = $user->UserName;
        $alias = $user->Alias;
        $money = intval($request["money"]);
        $tp=str_replace("BB","",$request->type);
        //限制额度 开始
        $money2=0;
        if($tp=='IN') $money2=$money;  //转入加上转入额度

        $BBIN_Limit=intval($sysConfig['BBIN_Limit']);
        if($sysConfig['BBIN_Repair']==1 or $sysConfig['BBIN']==0 or $user['BBIN']==0){
            echo "<script>alert('真人平台维护中，请稍候再试......');history.go(-1);</script>";
        }

        $date=date("Y-m-d");
        //"select sum(Gold) from BBIN_logs where Type='IN' and left(DateTime,10)='$date'";
        $row2  = DB::select("select sum(Gold) as IN_Money from BBIN_logs where Type='IN' ");
        $IN_Money=intval($row2[0]->IN_Money);

        //"select sum(Gold) as OUT_Money from BBIN_logs where Type='OUT' and left(DateTime,10)='$date'";
        $row2  = DB::select("select sum(Gold) as OUT_Money from BBIN_logs where Type='OUT'");
        $OUT_Money=intval($row2[0]->OUT_Money);
        if(($IN_Money+$money2-$OUT_Money)>$BBIN_Limit){
            echo "<h1>额度转换维护中，请联系客服人员</h1><script>alert('额度转换维护中，请联系客服人员');window.open('/kf.html');</script>";
        }

        //限制额度 结束
        $adddate=date("Y-m-d");
        $date=date("Y-m-d H:i:s");
        $curtype=$user['CurType'];
        $agents=$user['Agents'];
        $world=$user['World'];
        $corprator =$user['Corprator'];
        $super=$user['Super'];
        $admin=$user['Admin'];
        $phone=$user['Phone'];
        $name=$user['Alias'];

        if($BBIN_username==null or $BBIN_username==""){
            $WebCode =ltrim(trim($sysConfig['AG_User']));
            if(!preg_match("/^[A-Za-z0-9]{4,12}$/", $user['UserName'])){
                $BBIN_username = BBINUtils::getpassword_bbin(10);
            }else{
                $BBIN_username=trim($user['UserName']).BBINUtils::getpassword_bbin(1);
            }
            $BBIN_username='h07'.$WebCode.$BBIN_username;
            $BBIN_username=strtolower($BBIN_username);
            $BBIN_password=strtolower(BBINUtils::getpassword_bbin(10));


            $result= BBINUtils::Addmember_BBIN($BBIN_username,$BBIN_password,1);
            if($result['info']=='0'){
                $msql="update web_member_data set BBIN_User='".$BBIN_username."',BBIN_Pass='".$BBIN_password."' where UserName='".$username."'";
                $update = User::where('UserName', $username)->update(['BBIN_User' => $BBIN_username,
                'BBIN_Pass' => $BBIN_password, 'BBIN_Pass' => $BBIN_password,]);
                if($update){

                }else{

                };// or die($msql);
                echo("<script>alert('恭喜您，真人账号激活成功！');</script>");
            }else{
                //echo("<script>alert('网络异常，请与在线客服联系！');window.location='/app/member/ed.php?uid=".$uid."'</script>");
            }
        }
        if($tp=="IN"){  //转入
            if($money>$user['Money']){  //检查金额
                echo "<script language='javascript'>alert('转账金额不能大于会员余额!');history.go(-1);</script>";
                exit;
            }else{  //转入前扣款
                $assets= $user['Money'];//GetField($username,'Money');
                $user_id=$user['ID'];//GetField($username,'ID');
                Utils::ProcessUpdate($username);  //防止并发
                $result = DB::update("update web_member_data set Money=Money-$money where Username='$username'");

                if($result){
                    $balance = $assets-$money;
                    $datetime = date("Y-m-d H:i:s",time()+12*3600);
                    $bank_account = "转入BBIN真人账号";
                    $bank_Address = "";
                    $Order_Code='TK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);

                    $data = [
                        "Gold" => $money,
                        "previousAmount" => $assets,
                        "currentAmount" => $balance,
                        "AddDate" => $adddate,
                        "Type" => 'T',
                        "Type2" => "3",
                        "UserName" => $username,
                        "Agents" => $agents,
                        "World" => $world,
                        "Corprator" => $corprator,
                        "Super" => $super,
                        "Admin" => $admin,
                        "CurType" => $curtype,
                        "Date" => $date,
                        "Phone" => $phone,
                        "Contact" => '',
                        "Name" => $name,
                        "User" => $username,
                        "Bank" => "",
                        "Bank_Address" => $bank_Address,
                        "Bank_Account" => $bank_account,
                        "Order_Code" => $Order_Code,
                        "Checked" => 1,
                        "Music" => 1,
                    ];
                    $sys800 = new Sys800;
                    $deposit = $sys800->create($data);
                    if ($deposit){
                        //return response()->json(['success'=>$assets, 'user'=>$balance]);
                    }else{
                        //die ("操作失败!!!");
                    }
                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "转入BBIN真人平台",
                        "update_time" => $datetime,
                        "type" => "转入BBIN真人平台",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        $ouid=$result->id;
                    }else{
                        //die($money_log_sql);
                    }
                }
            }
        }

        if($tp=="OUT"){  //转出
            $money2= BBINUtils::getMoney_BBIN($BBIN_username, $BBIN_password); //获取真人余额
            if($money>$money2){
                return response()->json(['success'=>false, 'message'=> '转账金额不能大于真人账号余额!']);
            }
        }

        //转换前添加记录
        //$tr_sql="insert into `BBIN_logs` set Username='$username',Type='$tp',Gold='$money',Billno='0',DateTime='".date("Y-m-d H:i:s",time())."',Result='0',Checked='0'";

        $bbinLogData = [
            "Username" => $username,
            "Type" => $tp,
            "Gold" => $money,
            "Billno" => '0',
            "DateTime" => date("Y-m-d H:i:s",time()),
            "Result" => '0',
            "Checked" => '0',
        ];
        $bbinLog = new BBINLogs();
        $result = $bbinLog->create($bbinLogData);
        if ($result){
            $ouid2=$result->id;
        }else{

        }
        //转换操作
        $results= BBINUtils::Deposit_BBIN($BBIN_username,$BBIN_password,$money,$tp);
        $billno=$results['billno'];
        $result=0;
        if($results['info']=='0') $result=1;

        //更新状态
        //$tr_sql="update  `BBIN_logs` set Billno='$billno',Result='$result',Checked='$result' where id='$ouid2'";

        BBINLogs::where('id', $ouid2)->update(['Billno' => $billno,
        'Result' => $result, 'Checked' => $result,]);

        if($result==1){
            if($tp=='IN'){
                MoneyLog::where('id', $ouid)->update(['about' => '转入BBIN真人平台<br>billno:'.$billno]);
            }
            if($tp=='OUT'){
                $bank_account="BBIN真人账号转出";
                $Order_Code='CK'.date("YmdHis",time()+12*3600).mt_rand(1000,9999);
                $previousAmount=Utils::GetField($username,'Money');
                $currentAmount=$previousAmount+$money;
                $sql = "insert into web_sys800_data set Checked=1,Gold='$money',previousAmount='$previousAmount
                ',currentAmount='$currentAmount',AddDate='$adddate',Type='S',Type2=3,UserName='$username
                ',Agents='$agents',World='$world',Corprator='$corprator',Super='$super
                ',Admin='$admin',CurType='RMB',Date='$date',Name='$name',User='$username
                ',Bank_Account='$bank_account',Music=1,Order_Code='$Order_Code'";
                $data = [
                    "Checked" => 1,
                    "Gold" => $money,
                    "previousAmount" => $previousAmount,
                    "currentAmount" => $currentAmount,
                    "AddDate" => $adddate,
                    "Type" => 'S',
                    "Type2" => "3",
                    "UserName" => $username,
                    "Agents" => $agents,
                    "World" => $world,
                    "Corprator" => 'RMB',
                    "Super" => $super,
                    "Admin" => $admin,
                    "CurType" => $curtype,
                    "Date" => $date,
                    "Name" => $name,
                    "User" => $username,
                    "Bank_Account" => $bank_account,
                    "Music" => 1,
                    "Order_Code" => $Order_Code,
                ];
                $sys800 = new Sys800;
                $deposit = $sys800->create($data);
                if ($deposit){
                    //return response()->json(['success'=>$assets, 'user'=>$balance]);
                }else{
                    return response()->json(['success'=>$assets, 'message'=>"操作失败!!!"]);
                }

                $assets=Utils::GetField($username,'Money');
                $user_id=Utils::GetField($username,'ID');
                Utils::ProcessUpdate($username);  //防止并发
                $mysql="update web_member_data set Money=Money+$money where Username='$username'";
                $q1 = User::where('Username', $username)->update(['Money' => $assets+$money]);
                if($q1){
                    $balance=Utils::GetField($username,'Money');
                    $datetime=date("Y-m-d H:i:s",time()+12*3600);

                    $moneyLogData = [
                        "user_id" => $user_id,
                        "order_num" => $Order_Code,
                        "about" => "BBIN真人账号转出<br>billno:$billno",
                        "update_time" => $datetime,
                        "type" => "BBIN真人账号转出",
                        "order_value" => -$money,
                        "assets" => $assets,
                        "balance" => $balance,
                    ];

                    $moneyLog = new MoneyLog;
                    $result = $moneyLog->create($moneyLogData);
                    if ($result){
                        //$ouid=$result->id;
                    }else{
                        return response()->json(['success'=>$assets, 'message'=>'mon_log_insert error']);
                    }
                }
            }
            return response()->json(['success'=>$assets, 'message'=>'转账成功!']);
        }else{
            return response()->json(['success'=>$assets, 'message'=>'网络异常，请稍后再试!']);
        }
    }
