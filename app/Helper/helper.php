<?php

//use Thumbnail;

use Pawlox\VideoThumbnail\Facade\VideoThumbnail;

//广西十分彩开奖函数
function Ssc_Auto_GXSF($num , $type){
    $zh = $num[0]+$num[1]+$num[2]+$num[3]+$num[4];
    if($type==1){
        return $zh;
    }
    if($type==2){
        if($zh>=55){
            return '总和大';
        }else{
            return '总和小';
        }
    }
    if($type==3){
        if($zh%2==0){
            return '总和双';
        }else{
            return '总和单';
        }
    }
    if($type==4){
        if($num[0]>$num[4]){
            return '龙';
        }
        if($num[0]<$num[4]){
            return '虎';
        }
        if($num[0]==$num[4]){
            return '和';
        }
    }
    if($type==5){
        $hm         = array();
        $hm[]       = $num[0];
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        //echo $b;
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '1211' || $a == '11011' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='111' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==6){
        $hm         = array();
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '1211' || $a == '11011' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='111' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==7){
        $hm         = array();
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        $hm[]       = $num[4];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '1211' || $a == '11011' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='111' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
}
//广西十分彩单双
function Ssc_Ds_GXSF($ball){
    if($ball==21){
        return '和';
    }elseif($ball%2==0){
        return '双';
    }else{
        return '单';
    }
}

//广西十分彩大小
function Ssc_Dx_GXSF($ball){
    if($ball==21){
        return '和';
    }elseif($ball>=10){ 
        return '大';
    }elseif($ball<=9){
        return '小';
    }
}

function sorts($number,$p){
    $cc=0;
    foreach($number as $key=>$value){
        if(in_array($value-1,$number) or in_array($value+1,$number)){
            $cc++;
        }
    }
    if($cc>=$p){
        return true;
    }else{
        return false;
    }
}

//广东11选5开奖函数
function Ssc_Auto_GD11($num , $type){
    $zh = $num[0]+$num[1]+$num[2]+$num[3]+$num[4];
    if($type==1){
        return $zh;
    }
    if($type==2){
        if($zh>=31){
            return '总和大';
        }elseif($zh==30){
            return '总和和';
        }else{
            return '总和小';
        }
    }
    if($type==3){
        if($zh%2==0){
            return '总和双';
        }else{
            return '总和单';
        }
    }
    if($type==4){
        if($num[0]>$num[4]){
            return '龙';
        }
        if($num[0]<$num[4]){
            return '虎';
        }
        if($num[0]==$num[4]){
            return '和';
        }
    }
    if($type==5){
        $hm         = array();
        $hm[]       = $num[0];
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        //echo $b;
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '1211' || $a == '11011' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='111' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==6){
        $hm         = array();
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '1211' || $a == '11011' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='111' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==7){
        $hm         = array();
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        $hm[]       = $num[4];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '1211' || $a == '11011' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='111' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
}
//广东11选5单双
function Ssc_Ds_GD11($ball){
    if($ball==11){
        return '和';
    }elseif($ball%2==0){
        return '双';
    }else{
        return '单';
    }
}
//广东11选5大小
function Ssc_Dx_GD11($ball){
    if($ball==11){
        return '和';
    }elseif($ball>=6){
        return '大';
    }else{
        return '小';
    }
}

//广东11选5开奖函数
function Ssc_Auto_D3($num , $type){
    $zh = $num[0]+$num[1]+$num[2];
    if($type==1){
        return $zh;
    }
    if($type==2){
        if($zh>=14){
            return '总和大';
        }else{
            return '总和小';
        }
    }
    if($type==3){
        if($zh%2==0){
            return '总和双';
        }else{
            return '总和单';
        }
    }
    if($type==4){
        if($num[0]>$num[2]){
            return '龙';
        }
        if($num[0]<$num[2]){
            return '虎';
        }
        if($num[0]==$num[2]){
            return '和';
        }
    }
    if($type==5){
        $hm         = array();
        $hm[]       = $num[0];
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        //echo $b;
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '019' || $a == '089' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='09' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==6){
        $hm         = array();
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '019' || $a == '089' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='09' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==7){
        $hm         = array();
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        $hm[]       = $num[4];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[0]==$hm[2] && $hm[1]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[0]==$hm[2] || $hm[1]==$hm[2]){
            return '对子';
        }else if($a == '019' || $a == '089' || sorts($hm, 3)){
            return '顺子';
        }else if($b=='09' || sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    } 
}

//广东快乐10分开奖函数
function Klsf_Auto($num , $type){
    $zh = $num[0]+$num[1]+$num[2]+$num[3]+$num[4]+$num[5]+$num[6]+$num[7];
    if($type==1){
        return $zh;
    }
    if($type==2){
        if($zh>=85){
            return '总和大';
        }
        if($zh<=83){
            return '总和小';
        }
        if($zh==84){
            return '总和和';
        }
    }
    if($type==3){
        if($zh%2==0){
            return '总和双';
        }else{
            return '总和单';
        }
    }
    if($type==4){
        if($zh%10>=5){
            return '总和尾大';
        }else{
            return '总和尾小';
        }
    }
    if($type==5){
        if($num[0]>$num[7]){
            return '龙';
        }
        if($num[0]<$num[7]){
            return '虎';
        }
        if($num[0]==$num[7]){
            return '和';
        }
    }
}
//广东快乐10分单双
function Klsf_Ds($ball){
    if($ball%2==0){
        return '双';
    }else{
        return '单';
    }
}
//广东快乐10分大小
function Klsf_Dx($ball){
    if($ball>=11){
        return '大';
    }else{
        return '小';
    }
}
//广东快乐10分尾大小
function Klsf_Wdx($ball){
    if($ball%10>=5){
        return '尾大';
    }else{
        return '尾小';
    }
}
//广东快乐10分合单双
function Klsf_Hdx($ball){
    if(($ball%10+floor($ball/10))%2==0){
        return '合数双';
    }else{
        return '合数单';
    }
}
//广东快乐10分中发白
function Klsf_Zfb($ball){
    if($ball<=7){
        return '中';
    }else if($ball>=8 && $ball<=14){
        return '发';
    }else{
        return '白';
    }
}
//广东快乐10分东南西北
function Klsf_Dnxb($ball){
    if($ball%4==1){
        return '东';
    }else if($ball%4==2){
        return '南';
    }else if($ball%4==3){
        return '西';
    }else{
        return '北';
    }
}

//北京快乐8开奖函数
function Ssc_Auto_BJKN($num , $type){
    $zh=0;
    for($i=0;$i<20;$i++){
        $zh=$zh+$num[$i];
    }
    if($type==1){
        return $zh;
    }
    if($type==2){  //大小,810
        if($zh>810){
            return '总和大';
        }
        if($zh<810){
            return '总和小';
        }
        if($zh==810){
            return '总和810';
        }
    }
    if($type==3){  //单双
        if($zh%2==0){
            return '总和双';
        }else{
            return '总和单';
        }
    }

    if($type==4){  //奇偶和
        $cc=0;
        $dd=0;
        for($i=0;$i<20;$i++){
            if($num[$i]%2==0) $cc++;
            if($num[$i]%2==1) $dd++;
        }
        if($cc==$dd) return '和';
        if($cc>$dd)  return '偶';
        if($cc<$dd)  return '奇';
    }

    if($type==5){  //上下盘
        $cc=0;
        $dd=0;
        for($i=0;$i<20;$i++){
            if($num[$i]<=40) $cc++;
            if($num[$i]>40) $dd++;
        }
        if($cc==$dd) return '中';
        if($cc>$dd)  return '上';
        if($cc<$dd)  return '下';
    }       
}



function CheckNumber($ball){
    $ball_arr=array();
    $ball=rtrim($ball,',');
    $tt=explode(',',$ball);
    $flag=1;
    for($i=0;$i<count($tt);$i++){
        $number1=$tt[$i];
        $number2=intval($number1);
        if(strlen($number1)<>strlen($number2) or $number1<>abs($number2)){
            $flag=0;
        }
        if(in_array($number2,$ball_array)){
            $flag=0;
        }else{
            $ball_array[]=$number2;
        }
    }
    return $flag;
}

//重庆时时彩开奖函数
function Ssc_Auto($num , $type){
    $zh = $num[0]+$num[1]+$num[2]+$num[3]+$num[4];
    if($type==1){
        return $zh;
    }
    if($type==2){
        if($zh>=23){
            return '总和大';
        }else{
            return '总和小';
        }
    }
    if($type==3){
        if($zh%2==0){
            return '总和双';
        }else{
            return '总和单';
        }
    }
    if($type==4){
        if($num[0]>$num[4]){
            return '龙';
        }
        if($num[0]<$num[4]){
            return '虎';
        }
        if($num[0]==$num[4]){
            return '和';
        }
    }
    if($type==5){
        $hm         = array();
        $hm[]       = $num[0];
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[1]==$hm[2] && $hm[0]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[1]==$hm[2] || $hm[0]==$hm[2]){
            return '对子';
        }else if($a == '019' or $a == '089' or sorts($hm, 3)){
            return '顺子';
        }else if($b=='09' or sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==6){
        $hm         = array();
        $hm[]       = $num[1];
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[1]==$hm[2] && $hm[0]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[1]==$hm[2] || $hm[0]==$hm[2]){
            return '对子';
        }else if($a == '019' or $a == '089' or sorts($hm, 3)){
            return '顺子';
        }else if($b=='09' or sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    if($type==7){
        $hm         = array();
        $hm[]       = $num[2];
        $hm[]       = $num[3];
        $hm[]       = $num[4];
        sort($hm);
        $a = $hm[0].$hm[1].$hm[2];
        $b=$hm[0].$hm[2];
        if($hm[0]==$hm[1] && $hm[1]==$hm[2] && $hm[0]==$hm[2]){
            return '豹子';
        }else if($hm[0]==$hm[1] || $hm[1]==$hm[2] || $hm[0]==$hm[2]){
            return '对子';
        }else if($a == '019' or $a == '089' or sorts($hm, 3)){
            return '顺子';
        }else if($b=='09' or sorts($hm, 2)){
            return '半顺';
        }else{
            return '杂六';
        }
    }
    
    if($type==8){//斗牛
        $n1=$num[0]; //8
        $n2=$num[1]; //9
        $n3=$num[2]; //1
        $n4=$num[3]; //0
        $n5=$num[4]; //7
        $array=$num;
        $zh=0;
        //$zh1=$n1+$n2+$n3+$n4+$n5;
        $zh2=0;
        for($i=0;$i<5;$i++){
            //$i = 0;
            $zh=-1;         
            $j=$i+1;
            for($ii=$j;$ii<5;$ii++){
                // $ii = 1 ;
                $jj=$ii+1;
                $zh=-1; 
                for($iii=$jj;$iii<5;$iii++){
                    // $iii = 2;
                    $zh=$array[$i]+$array[$ii]+$array[$iii];
                    // $zh  = 10;
                    
                    if($zh==0 || $zh%10==0) {
                        
                        foreach ($array as $key => $value) {
                            if($key==$i)unset($array[$key]);
                            if($key==$ii)unset($array[$key]);
                            if($key==$iii)unset($array[$key]);
                        }
                        
                        foreach ($array as $key => $value) {
                            $zh2+=$value;
                        }
                        
                        //echo $zh."|".$zh2."<br>";
                        break;
                    }
                }
                if($zh==0 || $zh%10==0) break;
            }
            if($zh==0 || $zh%10==0) break;
        }
        //echo "--".$zh."|".$zh2."<br>";
        if($zh==0 || $zh%10==0){
            if($zh2>10){
                return "牛".($zh2-10);
            }else if(($zh+$zh2)==0 || $zh2==10){
                return "牛牛";
            }else{
                if($zh2==0){
                    return '牛牛';
                }else{
                    return "牛".$zh2;
                }
            }
        }else{
            return "无牛";
        }
    }

    if($type==9){//牛牛
        $n1=$num[0]; //1
        $n2=$num[1]; //1
        $n3=$num[2]; //7
        $n4=$num[3]; //4
        $n5=$num[4]; //6
        $array=$num;
        $zh=0;
        //$zh1=$n1+$n2+$n3+$n4+$n5;
        $zh2=0;
        for($i=0;$i<5;$i++){
            //$i = 0;
            $zh=-1;         
            $j=$i+1;
            for($ii=$j;$ii<5;$ii++){
                // $ii = 1 ;
                $jj=$ii+1;
                $zh=-1; 
                for($iii=$jj;$iii<5;$iii++){
                    // $iii = 2;
                    $zh=$array[$i]+$array[$ii]+$array[$iii];
                    // $zh  = 10;
                    
                    if($zh==0 || $zh%10==0) {
                        
                        foreach ($array as $key => $value) {
                            if($key==$i)unset($array[$key]);
                            if($key==$ii)unset($array[$key]);
                            if($key==$iii)unset($array[$key]);
                        }
                        
                        foreach ($array as $key => $value) {
                            $zh2+=$value;
                        }
                        
                        //echo $zh."|".$zh2."<br>";
                        break;
                    }
                }
                if($zh==0 || $zh%10==0) break;
            }
            if($zh==0 || $zh%10==0) break;
        }//echo "--".$zh."|".$zh2."<br>";
        if($zh==0 || $zh%10==0){
            if(($zh2-10)%2==0){
                return "牛双";
            }else if(($zh2-10)%2!=0){
                return "牛单";
            }else if(($zh+$zh2)==0 || $zh2==10){
                return "牛双";
            }
        }else
        {
            return "无牛";
        }
        
    }
    
    if($type==10){//牛牛
        $n1=$num[0]; //1
        $n2=$num[1]; //1
        $n3=$num[2]; //7
        $n4=$num[3]; //4
        $n5=$num[4]; //6
        $array=$num;
        $zh=0;
        //$zh1=$n1+$n2+$n3+$n4+$n5;
        $zh2=0;
        for($i=0;$i<5;$i++){
            //$i = 0;
            $zh=-1;         
            $j=$i+1;
            for($ii=$j;$ii<5;$ii++){
                // $ii = 1 ;
                $jj=$ii+1;
                $zh=-1; 
                for($iii=$jj;$iii<5;$iii++){
                    // $iii = 2;
                    $zh=$array[$i]+$array[$ii]+$array[$iii];
                    // $zh  = 10;
                    
                    if($zh==0 || $zh%10==0) {
                        
                        foreach ($array as $key => $value) {
                            if($key==$i)unset($array[$key]);
                            if($key==$ii)unset($array[$key]);
                            if($key==$iii)unset($array[$key]);
                        }
                        
                        foreach ($array as $key => $value) {
                            $zh2+=$value;
                        }
                        
                        //echo $zh."|".$zh2."<br>";
                        break;
                    }
                }
                if($zh==0 || $zh%10==0) break;
            }
            if($zh==0 || $zh%10==0) break;
        }//echo "--".$zh."|".$zh2."<br>";
        if($zh==0 || $zh%10==0){
            if($zh2>10 && ($zh2-10) > 5){
                return "牛大";
            }else if($zh2>10 && ($zh2-10) < 6){
                return "牛小";
            }else if(($zh+$zh2)==0 || $zh2==10){
                return "牛大";
            }else if($zh2<=10 && $zh2 > 5){
                return "牛大";
            }else if($zh2<=10 && $zh2 < 6){
                return "牛小";
            }

        }else
        {
            return "无牛";
        }
        
    }
}
//重庆时时彩单双
function Ssc_Ds($ball){
    if($ball%2==0){
        return '双';
    }else{
        return '单';
    }
}
//重庆时时彩大小
function Ssc_Dx($ball){
    if($ball>4){
        return '大';
    }else{
        return '小';
    }
}

//北京赛车PK拾开奖函数
function Bjsc_Auto($num , $type){
    $zh = $num[0]+$num[1];
    if($type==1){
        return $zh;
    }
    if($type==2){
        if($zh==11){
            return '和';
        }elseif($zh>11){
            return '大';
        }else{
            return '小';
        }
    }
    if($type==3){
        if($zh==11){
            return '和';
        }elseif($zh%2==0){
            return '双';
        }else{
            return '单';
        }
    }
    if($type==4){
        if($num[0]==$num[9]){
            return '和';
        }elseif($num[0]>$num[9]){
            return '龙';
        }else{
            return '虎';
        }
    }
    if($type==5){
        if($num[1]==$num[8]){
            return '和';
        }elseif($num[1]>$num[8]){
            return '龙';
        }else{
            return '虎';
        }
    }
    if($type==6){
        if($num[2]==$num[7]){
            return '和';
        }elseif($num[2]>$num[7]){
            return '龙';
        }else{
            return '虎';
        }
    }
    if($type==7){
        if($num[3]==$num[6]){
            return '和';
        }elseif($num[3]>$num[6]){
            return '龙';
        }else{
            return '虎';
        }
    }
    if($type==8){
        if($num[4]==$num[5]){
            return '和';
        }elseif($num[4]>$num[5]){
            return '龙';
        }else{
            return '虎';
        }
    }
}

//北京赛车PK拾单双
function Bjsc_Ds($ball){
    if($ball%2==0){
        return '双';
    }else{
        return '单';
    }
}

//北京赛车PK拾大小
function Bjsc_Dx($ball){
    if($ball>=6){
        return '大';
    }else{
        return '小';
    }
}

if (!function_exists('getXianEr')) {
    function getXianEr($type,$line,$memrow){
        $Bet_Trun=0;$BET_SO=0;$BET_SC=0;
        switch ($line){
        case 1:  //独赢
            $Bet_Trun=$memrow[$type."_Turn_M"];
            $BET_SO=$memrow[$type."_M_Bet"];
            $BET_SC=$memrow[$type."_M_Scene"];
            break;
        case 2: //让球
            $Bet_Trun=$memrow[$type."_Turn_R"];
            $BET_SO=$memrow[$type."_R_Bet"];
            $BET_SC=$memrow[$type."_R_Scene"];
            break;
        case 3:  //大小
            $Bet_Trun=$memrow[$type."_Turn_OU"];
            $BET_SO=$memrow[$type."_OU_Bet"];
            $BET_SC=$memrow[$type."_OU_Scene"];
            break;
        case 4:  //波胆
            $Bet_Trun=$memrow[$type."_Turn_PD"];
            $BET_SO=$memrow[$type."_PD_Bet"];
            $BET_SC=$memrow[$type."_PD_Scene"];
            break;
        case 5:  //单双
            $Bet_Trun=$memrow[$type."_Turn_EO"];
            $BET_SO=$memrow[$type."_EO_Bet"];
            $BET_SC=$memrow[$type."_EO_Scene"];
            break;
        case 6:  //总入球
            $Bet_Trun=$memrow[$type."_Turn_T"];
            $BET_SO=$memrow[$type."_T_Bet"];
            $BET_SC=$memrow[$type."_T_Scene"];
            break;
        case 7: //半全场
            $Bet_Trun=$memrow[$type."_Turn_F"];
            $BET_SO=$memrow[$type."_F_Bet"];
            $BET_SC=$memrow[$type."_F_Scene"];
            break;
        case 9: //滚球让球
            $Bet_Trun=$memrow[$type."_Turn_RE"];
            $BET_SO=$memrow[$type."_RE_Bet"];
            $BET_SC=$memrow[$type."_RE_Scene"];
            break;
        case 10: //滚球大小
            $Bet_Trun=$memrow[$type."_Turn_OU"];
            $BET_SO=$memrow[$type."_OU_Bet"];
            $BET_SC=$memrow[$type."_OU_Scene"];
            break;
        case 11: //半场独赢
            $Bet_Trun=$memrow[$type."_Turn_M"];
            $BET_SO=$memrow[$type."_M_Bet"];
            $BET_SC=$memrow[$type."_M_Scene"];
            break;
        case 12: //半场让球
            $Bet_Trun=$memrow[$type."_Turn_R"];
            $BET_SO=$memrow[$type."_R_Bet"];
            $BET_SC=$memrow[$type."_R_Scene"];
            break;
        case 13: //半场大小
            $Bet_Trun=$memrow[$type."_Turn_OU"];
            $BET_SO=$memrow[$type."_OU_Bet"];
            $BET_SC=$memrow[$type."_OU_Scene"];
            break;
        case 14: //半场波胆
            $Bet_Trun=$memrow[$type."_Turn_PD"];
            $BET_SO=$memrow[$type."_PD_Bet"];
            $BET_SC=$memrow[$type."_PD_Scene"];
            break;
        case 19: //半场滚球让球
            $Bet_Trun=$memrow[$type."_Turn_RE"];
            $BET_SO=$memrow[$type."_RE_Bet"];
            $BET_SC=$memrow[$type."_RE_Scene"];
            break;
        case 20: //半场滚球大小
            $Bet_Trun=$memrow[$type."_Turn_OU"];
            $BET_SO=$memrow[$type."_OU_Bet"];
            $BET_SC=$memrow[$type."_OU_Scene"];
            break;
        case 21: //滚球独赢
            $Bet_Trun=$memrow[$type."_Turn_M"];
            $BET_SO=$memrow[$type."_M_Bet"];
            $BET_SC=$memrow[$type."_M_Scene"];
            break;
        case 31: //半场滚球独赢
            $Bet_Trun=$memrow[$type."_Turn_M"];
            $BET_SO=$memrow[$type."_M_Bet"];
            $BET_SC=$memrow[$type."_M_Scene"];
            break;
        case 'PR': //过关
            $Bet_Trun=$memrow[$type."_Turn_PR"];
            $BET_SO=$memrow[$type."_PR_Bet"];
            $BET_SC=$memrow[$type."_PR_Scene"];
            break;
        case 'P3': //过关
            $Bet_Trun=$memrow[$type."_Turn_P3"];
            $BET_SO=$memrow[$type."_P3_Bet"];
            $BET_SC=$memrow[$type."_P3_Scene"];
            break;
        case 'FS': //冠军
            $Bet_Trun=$memrow["FS_Turn_FS"];
            $BET_SO=$memrow["FS_FS_Bet"];
            $BET_SC=$memrow["FS_FS_Scene"];
            break;
        }
        $result=array();
        $result['Bet_Trun']=$Bet_Trun;
        $result['BET_SO']=$BET_SO;
        $result['BET_SC']=$BET_SC;
        return $result;
    }
}

if(!function_exists("GetUrl_HG")){
    function GetUrl_HG($url,$post=null,$timeout=60,$refe_url=null,$cookie=null,$header=null,$ip_address=null){
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
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }
}

function show_voucher($line, $id, $web_system_data){
    $show_voucher = "";
    $ouid=$web_system_data['OUID'];
    $dtid=$web_system_data['DTID'];
    $pmid=$web_system_data['PMID'];
        switch($line){
    case 1:
    case 101:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 2:
    case 102:
    case 56:
    case 57:
    case 58:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 3:
    case 103:
    case 59:
    case 60:
    case 61:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 4:
        $show_voucher='DT'.($id+$dtid);
        break;
    case 5:
    case 105:
        $show_voucher='DT'.($id+$dtid);
        break;
    case 6:
        $show_voucher='DT'.($id+$dtid);
        break;
    case 7:
        $show_voucher='DT'.($id+$dtid);
        break;
    case 8:
        $show_voucher='PM'.($id+$pmid);
        break;
    case 9:
    case 50:
    case 51:
    case 52:
    case 150:
    case 151:
    case 152:
    case 156:
    case 157:
    case 158:
    case 109:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 10:
    case 110:
    case 53:
    case 54:
    case 55:
    case 153:
    case 154:
    case 155:
    case 159:
    case 160:
    case 161:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 11:
    case 111:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 12:
    case 112:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 13:
    case 113:
    case 120:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 14:
        $show_voucher='DT'.($id+$dtid);
        break;
    case 15:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 16:
        $show_voucher='DT'.($id+$dtid);
        break;
    case 19:
    case 119:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 20:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 21:
    case 121:
        $show_voucher='OU'.($id+$ouid);
        break;
    case 31:
    case 131:
        $show_voucher='OU'.($id+$ouid);
        break;
    }
    return $show_voucher;
}

if (!function_exists('filiter_team')) {
    function filiter_team($repteam){
        //$repteam=trim(str_replace(" ","",$repteam));
        $repteam=trim(str_replace("[H]","",$repteam));
        $repteam=trim(str_replace("[主]","",$repteam));
        $repteam=trim(str_replace("[中]","",$repteam));
        $repteam=trim(str_replace("[主]","",$repteam));
        $repteam=trim(str_replace("[中]","",$repteam));
        $repteam=trim(str_replace("[Home]","",$repteam));
        $repteam=trim(str_replace("[Mid]","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [上半场]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [下半场]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [上半場]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [下半場]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [1st]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=#990000> - [2nd]</font>","",$repteam));
        
        $repteam=trim(str_replace("<font color=gray> - [上半]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - [下半]</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (上半)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (下半)</font>","",$repteam));  
        $repteam=trim(str_replace("<font color=gray> - (第1节)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第2节)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第3节)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第4节)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第1節)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第2節)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第3節)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (第4節)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (1st Half)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (2nd Half)</font>","",$repteam));
        $repteam=trim(str_replace("<font color=gray> - (Q1)</font>","",$repteam));  
        $repteam=trim(str_replace("<font color=gray> - (Q2)</font>","",$repteam));  
        $repteam=trim(str_replace("<font color=gray> - (Q3)</font>","",$repteam));  
        $repteam=trim(str_replace("<font color=gray> - (Q4)</font>","",$repteam));
        $filiter_team=$repteam;
        return $filiter_team;
    }
}

if (!function_exists('change_rate')) {
    function change_rate($c_type, $c_rate){
        $t_rate =  '';
        switch($c_type){
        case 'A':
            $t_rate='0.03';
            break;
        case 'B':
            $t_rate='0.01';
            break;
        case 'C':
            $t_rate='0';
            break;
        case 'D':
            $t_rate='-0.01';
            break;
        }
        if ($c_rate!='' and $c_rate!='0'){
            $change_rate=number_format($c_rate-(float)$t_rate,3);
            if ($change_rate<=0 and $change_rate>=-0.03){
                $change_rate='';
            }
        }else{
            $change_rate='';
        }
        return $change_rate;
    }
}

function  get_other_ioratio($odd_type, $iorH, $iorC, $showior){
    $out=Array();
    if($iorH!="" ||$iorC!=""){
        $out =chg_ior($odd_type, $iorH, $iorC, $showior);
    }else{
        $out[0]=$iorH;
        $out[1]=$iorC;
    }
    return $out;
}

function chg_ior($odd_f, $iorH, $iorC, $showior){
    $ior=Array();
    if($iorH < 3) $iorH *=1000;
    if($iorC < 3) $iorC *=1000;
    $iorH=$iorH;
    $iorC=$iorC;
    switch($odd_f){
    case "H":   //香港變盤(輸水盤)
        $ior = get_HK_ior($iorH,$iorC);
        break;
    case "M":   //馬來盤
        $ior = get_MA_ior($iorH,$iorC);
        break;
    case "I" :  //印尼盤
        $ior = get_IND_ior($iorH,$iorC);
        break;
    case "E":   //歐洲盤
        $ior = get_EU_ior($iorH,$iorC);
        break;
    default:    //香港盤
        $ior[0]=$iorH ;
        $ior[1]=$iorC ;
    }
    $ior[0]/=1000;
    $ior[1]/=1000;
    $ior[0]=Decimal_point($ior[0],$showior);
    $ior[1]=Decimal_point($ior[1],$showior);
    return $ior;
}



/*
 * 換算成輸水盤賠率
 * @param H_ratio
 * @param C_ratio
 * @return
 */
function get_HK_ior($H_ratio,$C_ratio){
    $out_ior=Array();
    $line="";
    $lowRatio="";
    $nowRatio="";
    $highRatio="";
    $nowType="";
    if ($H_ratio <= 1000 && $C_ratio <= 1000){
        $out_ior[0]=$H_ratio;
        $out_ior[1]=$C_ratio;
        return $out_ior;
    }
    $line=2000 - ( $H_ratio + $C_ratio );
    if ($H_ratio > $C_ratio){ 
        $lowRatio=$C_ratio;
        $nowType = "C";
    }else{
        $lowRatio = $H_ratio;
        $nowType = "H";
    }
    if (((2000 - $line) - $lowRatio) > 1000){
        //對盤馬來盤
        $nowRatio = ($lowRatio + $line) * (-1);
    }else{
        //對盤香港盤
        $nowRatio=(2000 - $line) - $lowRatio;   
    }
    if ($nowRatio < 0){
        $highRatio = (abs(1000 / $nowRatio) * 1000) ;
    }else{
        $highRatio = (2000 - $line - $nowRatio) ;
    }
    if ($nowType == "H"){
        $out_ior[0]=$lowRatio;
        $out_ior[1]=$highRatio;
    }else{
        $out_ior[0]=$highRatio;
        $out_ior[1]=$lowRatio;
    }
    return $out_ior;
}


/*
 * 換算成馬來盤賠率
 * @param H_ratio
 * @param C_ratio
 * @return
 */
function get_MA_ior( $H_ratio, $C_ratio){
    $out_ior=Array();
    $line="";
    $lowRatio="";
    $highRatio="";
    $nowType="";
    if (($H_ratio <= 1000 && $C_ratio <= 1000)){
        $out_ior[0]=$H_ratio;
        $out_ior[1]=$C_ratio;
        return $out_ior;
    }
    $line=2000 - ( $H_ratio + $C_ratio );
    if ($H_ratio > $C_ratio){ 
        $lowRatio = $C_ratio;
        $nowType = "C";
    }else{
        $lowRatio = $H_ratio;
        $nowType = "H";
    }
    $highRatio = ($lowRatio + $line) * (-1);
    if ($nowType == "H"){
        $out_ior[0]=$lowRatio;
        $out_ior[1]=$highRatio;
    }else{
        $out_ior[0]=$highRatio;
        $out_ior[1]=$lowRatio;
    }
    return $out_ior;
}

/*
 * 換算成印尼盤賠率
 * @param H_ratio
 * @param C_ratio
 * @return
 */
function get_IND_ior( $H_ratio, $C_ratio){
    $out_ior=Array();
    $out_ior = get_HK_ior($H_ratio,$C_ratio);
    $H_ratio=$out_ior[0];
    $C_ratio=$out_ior[1];
    $H_ratio /= 1000;
    $C_ratio /= 1000;
    if($H_ratio < 1){
        $H_ratio=(-1) / $H_ratio;
    }
    if($C_ratio < 1){
        $C_ratio=(-1) / $C_ratio;
    }
    $out_ior[0]=$H_ratio*1000;
    $out_ior[1]=$C_ratio*1000;
    return $out_ior;
}

/*
 * 換算成歐洲盤賠率
 * @param H_ratio
 * @param C_ratio
 * @return
 */
function get_EU_ior($H_ratio, $C_ratio){
    $out_ior=Array();
    $out_ior = get_HK_ior($H_ratio,$C_ratio);
    $H_ratio=$out_ior[0];
    $C_ratio=$out_ior[1];       
    $out_ior[0]=$H_ratio+1000;
    $out_ior[1]=$C_ratio+1000;
    return $out_ior;
}
/*
去正負號做小數第幾位捨去
進來的值是小數值
*/
function Decimal_point($tmpior,$show){
    $sign="";
    $sign =(($tmpior < 0)?"Y":"N");
    $tmpior = (floor(abs($tmpior) * $show + 1 / $show )) / $show;
    return ($tmpior * (($sign =="Y")? -1:1));
}
/*
 公用 FUNC
*/
function number($vals,$points){ //小數點位數
    $cmd=Array();
    $cmd=split(".",$vals);
    $length=strlen($cmd[1]);
    if (count($cmd)>1){
        for ($ii=0;$ii<($points-$length);$ii++) $vals=$vals."0";
    }else{
        $vals=$vals+".";
        for ($ii=0;$ii<$points;$ii++) $vals=$vals."0";
    }
    return $vals;
}

if (!function_exists('attention')) {
    function attention($msg,$uid,$langx){
        $key=rand(1,199);
        if ($langx=='zh-cn'){
            $confirm='确定';
        }else if ($langx=='zh-tw'){
            $confirm='確定';
        }else if ($langx=='en-us' or $langx=='th-tis'){
            $confirm=' OK ';
        }
        $test=$test."<html>";
        $test=$test."<head>";
        $test=$test."<title>Attention</title>";
        $test=$test."<meta http-equiv=Content-Type content=text/html; charset=utf-8>";
        $test=$test."<link rel=stylesheet href=/style/member/mem_order.css type=text/css>";
        $test=$test."</head>";
        $test=$test."<body id=BLUE>";
        $test=$test."<div>";
        $test=$test."<p>$msg$key</p>";
        $test=$test."<p><input type=button name='check' value='$confirm' onClick=javascript:location='".$_SERVER['HTTP_REFERER']."' height='20' class='yes'></p>";
        $test=$test."</div>";
        $test=$test."</body>";
        $test=$test."</html>";
        return $test;
    }
}

if (!function_exists('generatePasswordResetUrl')) {
    function generatePasswordResetUrl($forgotPasswordMail, $email)
    {
        $token = generateRandomToken(50, $email);

        $tokenMailObj = $forgotPasswordMail::where('email', $email)->first();
        if (!$tokenMailObj) {
            $tokenMailObj = new $forgotPasswordMail;
        }

        $tokenMailObj->email = $email;
        $tokenMailObj->token = $token;

        $currentTime = date("Y-m-d H:i:s");
        $mailExpireTime = date('Y-m-d H:i:s', strtotime('+60 minutes', strtotime($currentTime)));

        $tokenMailObj->expired_at = $mailExpireTime;
        $tokenMailObj->save();

        return route('password.reset', [$token, 'email' => $email]);
    }
}

if (!function_exists('validation_error_response')) {
    function validation_error_response($errors)
    {
        $response = [];
        $counter = 0;
        foreach ($errors as $key => $value) {
            if ($counter > 0) {
                break;
            }

            $errorMessage = $value[0];
        }

        $response['message'] = $errorMessage;
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        return $response;
    }
}

if (!function_exists('base64_to_image')) {
    function base64_to_image($base64_string)
    {
        // Define the Base64 value you need to save as an image
        $b64 = $base64_string;

        // Obtain the original content (usually binary data)
        $bin = base64_decode($b64);

        // Gather information about the image using the GD library
        $size = getImageSizeFromString($bin);

        // Check the MIME type to be sure that the binary data is an image
        if (empty($size['mime']) || strpos($size['mime'], 'image/') !== 0) {
            die('Base64 value is not a valid image');
        }

        // Mime types are represented as image/gif, image/png, image/jpeg, and so on
        // Therefore, to extract the image extension, we subtract everything after the “image/” prefix
        $ext = substr($size['mime'], 6);

        // Make sure that you save only the desired file extensions
        if (!in_array($ext, ['png', 'gif', 'jpeg'])) {
            die('Unsupported image type');
        }

        $file_name = "chat_image_" . time() . "." . $ext;

        // Specify the location where you want to save the image
        $img_file = IMAGE_UPLOAD_PATH . $file_name;

        // Save binary data as raw data (that is, it will not remove metadata or invalid contents)
        // In this case, the PHP backdoor will be stored on the server
        file_put_contents($img_file, $bin);

        return [
            'file_name' => $file_name,
            'file_type' => $ext,
        ];
    }
}

if (!function_exists('uploadImages')) {
    function uploadImages($images = [], $destinationPath = '')
    {
        $image_path_data = [];

        foreach ($images as $key => $file) {
            $fileName = time() . '-' . $file->getClientOriginalName();

            $fileName = str_replace(" ", "_", $fileName);

            $extension = $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);

            $mime = $file->getClientMimeType();

            $fileType = "";
            if (strstr($mime, "video/")) {
                $fileType = "video";
            } else if (strstr($mime, "image/")) {
                $fileType = "image";
            } else if (strstr($mime, "audio/")) {
                $fileType = "audio";
            }

            $image_path_data[$key] = [
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_extension' => $extension ?? '',
            ];
        }

        return $image_path_data;
    }
}

if (!function_exists('uploadSingleImage')) {
    function uploadSingleImage($file, $destinationPath = '')
    {
        $fileName = time() . '-' . $file->getClientOriginalName();
        $fileName = str_replace(" ", "_", $fileName);
        $file->move($destinationPath, $fileName);

        return $fileName;
    }
}

if (!function_exists('createThumbnail')) {
    function createThumbnail($filePath = '', $fileName = '', $userId = '')
    {
        $thumbnailName = $userId . time() . '_thumbnail.jpg';

        $thumbnailStatus = VideoThumbnail::getThumbnail($filePath . $fileName, $filePath, $thumbnailName, 2);

        if ($thumbnailStatus) {
            return $thumbnailName;
        }
        return '';
    }
}

if (!function_exists('generateNumericOTP')) {
    function generateNumericOTP($n)
    {

        // Take a generator string which consist of
        // all numeric digits
        $generator = "1357902468";

        // Iterate for n-times and pick a single character
        // from generator and append it to $result

        // Login for generating a random character from generator
        //     ---generate a random number
        //     ---take modulus of same with length of generator (say i)
        //     ---append the character at place (i) from generator to result

        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        // Return result
        return $result;
    }
}

if (!function_exists('addMinutesToTime')) {
    function addMinutesToTime($timeData = [])
    {
        if (!isset($timeData['time'])) {
            $time = new DateTime();
        } else {
            $time = new DateTime($timeData['time']);
        }

        if (!isset($timeData['minute'])) {
            $minutes_to_add = 2;
        } else {
            $minutes_to_add = (int)$timeData['minute'];
        }

        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

        $stamp = $time->format('Y-m-d H:i:s');

        return $stamp;
    }
}

if (!function_exists('object_to_array')) {
    function object_to_array($obj, &$arr)
    {
        if (!is_object($obj) && !is_array($obj)) {
            $arr = $obj;
            return $arr;
        }

        foreach ($obj as $key => $value) {
            if (!empty($value)) {
                $arr[$key] = array();
                object_to_array($value, $arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }

        return $arr;
    }
}

if (!function_exists('sort_days')) {
    function sort_days($days = [])
    {
        $daysArr = [];

        if (!isset($days['monday']['is_opened'])) {
            $daysArr['monday']['is_opened'] = FALSE;
        } else {
            $daysArr['monday']['is_opened'] = TRUE;
            $daysArr['monday']['open'] = $days['monday']['open'];
            $daysArr['monday']['close'] = $days['monday']['close'];
        }

        if (!isset($days['tuesday']['is_opened'])) {
            $daysArr['tuesday']['is_opened'] = FALSE;
        } else {
            $daysArr['tuesday']['is_opened'] = TRUE;
            $daysArr['tuesday']['open'] = $days['tuesday']['open'];
            $daysArr['tuesday']['close'] = $days['tuesday']['close'];
        }

        if (!isset($days['wednesday']['is_opened'])) {
            $daysArr['wednesday']['is_opened'] = FALSE;
        } else {
            $daysArr['wednesday']['is_opened'] = TRUE;
            $daysArr['wednesday']['open'] = $days['wednesday']['open'];
            $daysArr['wednesday']['close'] = $days['wednesday']['close'];
        }

        if (!isset($days['thursday']['is_opened'])) {
            $daysArr['thursday']['is_opened'] = FALSE;
        } else {
            $daysArr['thursday']['is_opened'] = TRUE;
            $daysArr['thursday']['open'] = $days['thursday']['open'];
            $daysArr['thursday']['close'] = $days['thursday']['close'];
        }

        if (!isset($days['friday']['is_opened'])) {
            $daysArr['friday']['is_opened'] = FALSE;
        } else {
            $daysArr['friday']['is_opened'] = TRUE;
            $daysArr['friday']['open'] = $days['friday']['open'];
            $daysArr['friday']['close'] = $days['friday']['close'];
        }

        if (!isset($days['saturday']['is_opened'])) {
            $daysArr['saturday']['is_opened'] = FALSE;
        } else {
            $daysArr['saturday']['is_opened'] = TRUE;
            $daysArr['saturday']['open'] = $days['saturday']['open'];
            $daysArr['saturday']['close'] = $days['saturday']['close'];
        }

        if (!isset($days['sunday']['is_opened'])) {
            $daysArr['sunday']['is_opened'] = FALSE;
        } else {
            $daysArr['sunday']['is_opened'] = TRUE;
            $daysArr['sunday']['open'] = $days['sunday']['open'];
            $daysArr['sunday']['close'] = $days['sunday']['close'];
        }

        return $daysArr;
    }
}

if (!function_exists('send_push_notification')) {
    function send_push_notification($notificationData = [])
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            "registration_ids" => $notificationData['device_token'],
            "notification" => array(
                "body" => $notificationData['message'],
                "sendby" => $notificationData['send_by'],
                "type" => $notificationData['type'],
                "content-available" => 1,
                "badge" => $notificationData['badge'] ?? 1,
                "sound" => "default",
            ),
            "data" => array(
                "body" => $notificationData['message'],
                "sendby" => $notificationData['send_by'],
                "type" => $notificationData['type'],
                "content-available" => 1,
                "badge" => $notificationData['badge'] ?? 1,
                "sound" => "default",
            ),
            "priority" => 10
        );

        if (isset($notificationData['metadata']) && !empty($notificationData['metadata'])) {
            $fields['notification']['metadata'] = $notificationData['metadata'];
            $fields['data']['metadata'] = $notificationData['metadata'];
        }

        //print_pre($fields);
        $fields = json_encode($fields);
        $headers = array(
            //'Authorization: key=' . PUSH_NOTIFICATION_SERVER_KEY,
            'Authorization: key=' . config('mail.push_notification.key'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('create_and_save_stripe_customer_id')) {
    function create_and_save_stripe_customer_id($array = [])
    {
        echo "<pre>";
        print_r($array);
        die;
    }
}

if (!function_exists('print_pre')) {
    function print_pre($array = [])
    {
        echo "<pre>";
        print_r($array);
        die;
    }
}

if (!function_exists('x_week_range')) {
    function x_week_range($date = NULL)
    {
        $date = $date ?? date('Y-m-d');
        $day = date('N', strtotime($date));
        $week_start = date('Y-m-d', strtotime('-' . ($day - 1) . ' days', strtotime($date)));
        $week_end = date('Y-m-d', strtotime('+' . (7 - $day) . ' days', strtotime($date)));

        return [$week_start, $week_end];
    }
}

if (!function_exists('get_setting')) {
    function get_setting()
    {
        $settingsObj = \App\Models\Setting::first();

        if ($settingsObj) {
            return $settingsObj->settings;
        }

        return [];
    }
}

if (!function_exists('get_setting_by_key')) {
    function get_setting_by_key($settingName = '', $settingsKey = '')
    {
        $settingsObj = \App\Models\Setting::first();

        if ($settingsObj) {
            $settings = (array) $settingsObj->settings;

            if (!empty($settingName)) {
                if (isset($settings[$settingName])) {
                    if (isset($settings[$settingName]->$settingsKey)) {
                        return $settings[$settingName]->$settingsKey;
                    }
                }
            }
        }

        return '';
    }
}

if (!function_exists('getDatesFromRange')) {
    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }
}

if (!function_exists('getClosest')) {
    function getClosest($search, $arr)
    {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }
        return $closest;
    }
}

if (!function_exists('saveGeolocation')) {
    function saveGeolocation($db, $table, $resourceId, $lat = NULL, $lng = NULL)
    {
        if ($lat && $lng) {
            $db::insert("UPDATE $table SET geolocation = ST_MakePoint($lng, $lat) WHERE id = '$resourceId'");
        }
    }
}

function getWeeks($today = null, $scheduleMonths = 6)
{

    $today = !is_null($today) ? \Carbon\Carbon::createFromFormat('Y-m-d', $today) : \Carbon\Carbon::now();

    $startDate = \Carbon\Carbon::instance($today)->startOfMonth()->startOfWeek()->subDay(); // start on Sunday
    $endDate = \Carbon\Carbon::instance($startDate)->addMonths($scheduleMonths)->endOfMonth();
    $endDate->addDays(6 - $endDate->dayOfWeek);

    $epoch = \Carbon\Carbon::createFromTimestamp(0);
    $firstDay = $epoch->diffInDays($startDate);
    $lastDay = $epoch->diffInDays($endDate);

    $week = 0;
    $monthNum = $today->month;
    $yearNum = $today->year;
    $prevDay = null;
    $theDay = $startDate;
    $prevMonth = $monthNum;

    $data = array();

    while ($firstDay < $lastDay) {

        if (($theDay->dayOfWeek == \Carbon\Carbon::SUNDAY) && (($theDay->month > $monthNum) || ($theDay->month == 1))) $monthNum = $theDay->month;
        if ($prevMonth > $monthNum) $yearNum++;

        $theMonth = \Carbon\Carbon::createFromFormat("Y-m-d", $yearNum . "-" . $monthNum . "-01")->format('F Y');

        if (!array_key_exists($theMonth, $data)) $data[$theMonth] = array();
        if (!array_key_exists($week, $data[$theMonth])) $data[$theMonth][$week] = array(
            'day_range' => '',
        );

        if ($theDay->dayOfWeek == \Carbon\Carbon::SUNDAY) $data[$theMonth][$week]['day_range'] = sprintf("%d-", $theDay->day);
        if ($theDay->dayOfWeek == \Carbon\Carbon::SATURDAY) $data[$theMonth][$week]['day_range'] .= sprintf("%d", $theDay->day);

        $firstDay++;
        if ($theDay->dayOfWeek == \Carbon\Carbon::SATURDAY) $week++;
        $theDay = $theDay->copy()->addDay();
        $prevMonth = $monthNum;
    }

    $totalWeeks = $week;

    return array(
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totalWeeks' => $totalWeeks,
        'schedule' => $data,
    );
}

function weekOfMonth($date)
{

    $firstOfMonth = strtotime(date("Y-m-01", $date));
    $lastWeekNumber = (int)date("W", $date);
    $firstWeekNumber = (int)date("W", $firstOfMonth);
    if (12 === (int)date("m", $date)) {
        if (1 == $lastWeekNumber) {
            $lastWeekNumber = (int)date("W", ($date - (7 * 24 * 60 * 60))) + 1;
        }
    } elseif (1 === (int)date("m", $date) and 1 < $firstWeekNumber) {
        $firstWeekNumber = 0;
    }
    return $lastWeekNumber - $firstWeekNumber + 1;
}

function weeks($month, $year)
{
    $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
    return weekOfMonth(strtotime($year . '-' . $month . '-' . $lastday));
}

function generateRandomToken($length = 10, $string = 'xyz')
{
    $characters = $string . '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' . time();
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function random_color_part()
{
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color()
{
    return random_color_part() . random_color_part() . random_color_part();
}

function array_values_recursive($arr)
{
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $arr[$key] = array_values($value);
        }
    }

    return $arr;
}

function generate_random_color($i = 0)
{

    $colors = [
        "rgb(47, 76, 221)",
        "rgb(43, 193, 85)",
        "rgb(255, 109, 77)",
        "rgb(255, 152, 0)",
        "rgb(62, 73, 84)",
        "rgb(247, 43, 80)",
    ];
    return $colors[$i];
}

function unique_multidimensional_array($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}
if (!function_exists('updateLatLngDeviceToken')) {
    function updateLatLngDeviceToken($resource, $requestData = [], $db = NULL)
    {
        $resource->device_token = $requestData['device_token'] ?? $resource->device_token;
        $resource->device_type = $requestData['device_type'] ?? $resource->device_type;
        $resource->lat = $requestData['lat'] ?? $resource->lat;
        $resource->lng = $requestData['lng'] ?? $resource->lng;
        $resource->save();

        $lat = $resource->lat;
        $lng = $resource->lng;

        if ($lat && $lng) {
            $db::insert("UPDATE users SET geolocation = ST_MakePoint($lng, $lat) WHERE id = '" . $resource->id . "'");
        }
    }
}
