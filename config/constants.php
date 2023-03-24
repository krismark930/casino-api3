<?php

// ============== order variables ======================

defined('ORDER_FT') or define("ORDER_FT", '足球');
defined('ORDER_Other') or define("ORDER_Other", '网球');
defined('ORDER_TN') or define("ORDER_TN", '排球');
defined('ORDER_VB') or define("ORDER_VB", '棒球');
defined('ORDER_BS') or define("ORDER_BS", '棒球');
defined('Order_Basketball') or define("Order_Basketball", '篮球');
defined('ORDER_EARLY_MARKET') or define("ORDER_EARLY_MARKET", '早餐');
defined('Order_1_x_2_betting_order') or define("Order_1_x_2_betting_order", '单式独赢交易单');
defined('Order_Handicap_betting_order') or define("Order_Handicap_betting_order", '单式让球交易单');
defined('Order_Over_Under_betting_order') or define("Order_Over_Under_betting_order", '单式大小交易单');
defined('Order_Odd_Even_betting_order') or define("Order_Odd_Even_betting_order", '单双交易单');
defined('Order_1st_Half_1_x_2_betting_order') or define("Order_1st_Half_1_x_2_betting_order", '上半场独赢交易单');
defined('Order_1st_Half_Handicap_betting_order') or define("Order_1st_Half_Handicap_betting_order", '上半场让球交易单');
defined('Order_1st_Half_Over_Under_betting_order') or define("Order_1st_Half_Over_Under_betting_order", '上半场大小交易单');
defined('Order_Running_1_x_2_betting_order') or define("Order_Running_1_x_2_betting_order", '滚球独赢交易单');
defined('Order_Running_Ball_betting_order') or define("Order_Running_Ball_betting_order", '滚球交易单');
defined('Order_Running_Ball_Over_Under_betting_order') or define("Order_Running_Ball_Over_Under_betting_order", '滚球大小交易单');
defined('Order_1st_Half_Running_1_x_2_betting_order') or define("Order_1st_Half_Running_1_x_2_betting_order", '上半场滚球独赢交易单');
defined('Order_1st_Half_Running_Ball_betting_order') or define("Order_1st_Half_Running_Ball_betting_order", '上半滚球交易单');
defined('Order_1st_Half_Running_Ball_Over_Under_betting_order') or define("Order_1st_Half_Running_Ball_Over_Under_betting_order", '足球上半滚球大小交易单');
defined('Order_Correct_Score_betting_order') or define("Order_Correct_Score_betting_order", '波胆交易单');
defined('Order_1st_Half_Correct_Score_betting_order') or define("Order_1st_Half_Correct_Score_betting_order", '入球数交易单');
defined('Order_Half_Full_Time_betting_order') or define("Order_Half_Full_Time_betting_order", '半全场交易单');
defined('Order_Mix_Parlay_betting_order') or define("Order_Mix_Parlay_betting_order", '综合过关交易单');
defined('Order_1_x_2_Parlay_betting_order') or define("Order_1_x_2_Parlay_betting_order", '标准过关交易单');
defined('Order_Handicap_Parlay_betting_order') or define("Order_Handicap_Parlay_betting_order", '让分过关交易单');
defined('Order_betting_order') or define("Order_betting_order", '交易单');
defined('Order_Login_Name') or define("Order_Login_Name", '帐户名称：');
defined('Order_Credit_line') or define("Order_Credit_line", '可用额度：');
defined('Order_Currency') or define("Order_Currency", '使用币别：');
defined('Order_The_maximum_payout_is_x_per_bet') or define("Order_The_maximum_payout_is_x_per_bet", '每注最高派彩以RMB$1000000为上限');
defined('Order_There_is_a_maximum_wager_limit_on_this_game_x_restriction') or define("Order_There_is_a_maximum_wager_limit_on_this_game_x_restriction", '本场有单注最高<B><*****></B> 限制!!');
defined('Order_1st_Half') or define("Order_1st_Half", '上半');
defined('Order_2nd_Half') or define("Order_2nd_Half", '下半');
defined('Order_1st_Quarter') or define("Order_1st_Quarter", '第二节');
defined('Order_3rd_Quarter') or define("Order_3rd_Quarter", '第三节');
defined('Order_4th_Quarter') or define("Order_4th_Quarter", '第四节');
defined('Order_Odd') or define("Order_Odd", '单');
defined('Order_Even') or define("Order_Even", '双');
defined('Order_This_odd_is_the_latest') or define("Order_This_odd_is_the_latest", '此为最新赔率');
defined('Order_Other_Score') or define("Order_Other_Score", '其它比分');
defined('Order_Mode') or define("Order_Mode", '模式：');
defined('Order_Delete') or define("Order_Delete", '删除');
defined('Order_single_wager') or define("Order_single_wager", '单注');
defined('Order_Bet_Amount') or define("Order_Bet_Amount", '交易金额：');
defined('Order_Estimated_Payout') or define("Order_Estimated_Payout", '可赢金额：');
defined('Order_Minimum') or define("Order_Minimum", '最低限额：');
defined('Order_Single_bet_limit') or define("Order_Single_bet_limit", '单注限额：');
defined('Order_Maximum') or define("Order_Maximum", '单场最高：');
defined('Order_Cancel') or define("Order_Cancel", '取消交易');
defined('Order_Confirm') or define("Order_Confirm", '确定交易');
defined('Order_Bet_success') or define("Order_Bet_success", '交易成功单号：');
defined('Order_Quit') or define("Order_Quit", '离开');
defined('Order_Print') or define("Order_Print", '列印');
defined('Order_Pending') or define("Order_Pending", '正在确认中');
defined('Order_Confirmed') or define("Order_Confirmed", '确认');
defined('Order_Please_check_transaction_record') or define("Order_Please_check_transaction_record", '请至交易状况查询');
defined('Order_This_match_is_closed_Please_try_again') or define("Order_This_match_is_closed_Please_try_again", '赛程已关闭,无法进行交易!!');
defined('Order_This_match_is_turned_to_Running_Ball_Please_wager_in_Running_Ball') or define("Order_This_match_is_turned_to_Running_Ball_Please_wager_in_Running_Ball", '本场次已转至走地盘口,请至走地!!');
defined('Order_The_game_is_covered_same_teams_Please_reset_again') or define("Order_The_game_is_covered_same_teams_Please_reset_again", '赛事重覆，请重新选择!!');
defined('Order_Odd_changed_please_bet_again') or define("Order_Odd_changed_please_bet_again", '赔率已变动,请重新下注!!');
defined('Order_Running_Ball_is_temporary_not_accepted_wagering') or define("Order_Running_Ball_is_temporary_not_accepted_wagering", '暂时停止交易(走地)');

// ================================= Score Constant =================================== //

defined('Score1') or define("Score1", '取消');
defined('Score2') or define("Score2", '赛事腰斩');
defined('Score3') or define("Score3", '赛事改期');
defined('Score4') or define("Score4", '赛事延期');
defined('Score5') or define("Score5", '赛事延赛');
defined('Score6') or define("Score6", '赛事取消');
defined('Score7') or define("Score7", '赛事无PK加时');
defined('Score8') or define("Score8", '球员弃权');
defined('Score9') or define("Score9", '队名错误');
defined('Score10') or define("Score10", '主客场错误');
defined('Score11') or define("Score11", '先发投手更换');
defined('Score12') or define("Score12", '选手更换');
defined('Score13') or define("Score13", '联赛名称错误');
defined('Score14') or define("Score14", '');
defined('Score15') or define("Score15", '');
defined('Score16') or define("Score16", '');
defined('Score17') or define("Score17", '');
defined('Score18') or define("Score18", '');
defined('Score19') or define("Score19", '提前开赛');
defined('Score20') or define("Score20", '[注单确认]');
defined('Score21') or define("Score21", '[取消]');
defined('Score22') or define("Score22", '[赛事腰斩]');
defined('Score23') or define("Scor23", '[赛事改期]');
defined('Score24') or define("Score24", '[赛事延期]');
defined('Score25') or define("Score25", '[赛事延赛]');
defined('Score26') or define("Score26", '[赛事取消]');
defined('Score27') or define("Score27", '[赛事无PK加时]');
defined('Score28') or define("Score28", '[球员弃权]');
defined('Score29') or define("Score29", '[队名错误]');
defined('Score30') or define("Score30", '[主客场错误]');
defined('Score31') or define("Score31", '[先发投手更换]');
defined('Score32') or define("Score32", '[选手更换]');
defined('Score33') or define("Score33", '[联赛名称错误]');
defined('Score34') or define("Score34", '[盘口错误]');
defined('Score35') or define("Score35", '[提前开赛]');
defined('Score36') or define("Score36", '[比分错误]');
defined('Score37') or define("Score37", '[未接受注单]');
defined('Score38') or define("Score38", '[进球取消]');
defined('Score39') or define("Score39", '[红卡取消]');
defined('Score40') or define("Score40", '[非正常投注]');
defined('Score41') or define("Score41", '[赔率错误]');

// ======================================== Super Admin Account ================================================
defined('SUPER_ADMIN_EMAIL') or define("SUPER_ADMIN_EMAIL", 'cepochmobiledevelopers2@gmail.com');
defined('SUPER_ADMIN_USERNAME') or define("SUPER_ADMIN_USERNAME", 'cepochdevelopers');
defined('SUPER_ADMIN_FIRST_NAME') or define("SUPER_ADMIN_FIRST_NAME", 'Super');
defined('SUPER_ADMIN_LAST_NAME') or define("SUPER_ADMIN_LAST_NAME", 'Admin');

// ======================================== Admin Account ======================================================
defined('ADMIN_EMAIL') or define("ADMIN_EMAIL", 'Yourbestapp@gmail.com');
defined('ADMIN_USERNAME') or define("ADMIN_USERNAME", 'admin');
defined('ADMIN_FIRST_NAME') or define("ADMIN_FIRST_NAME", 'Admin');
defined('ADMIN_LAST_NAME') or define("ADMIN_LAST_NAME", 'Account');

// ======================================== Developer Account ==================================================
defined('DEVELOPER_EMAIL') or define("DEVELOPER_EMAIL", 'developers@admin.com');
defined('DEVELOPER_USERNAME') or define("DEVELOPER_USERNAME", 'developers');
defined('DEVELOPER_FIRST_NAME') or define("DEVELOPER_FIRST_NAME", 'Developer');
defined('DEVELOPER_LAST_NAME') or define("DEVELOPER_LAST_NAME", 'Account');

// ======================================== App Details ========================================================
defined('APP_NAME') or define("APP_NAME", 'YourBest');
defined('MAIL_FROM_EMAIL') or define("MAIL_FROM_EMAIL", 'no-reply@yourbest.com');
defined('CONTACT_MAIL_FROM_EMAIL') or define("CONTACT_MAIL_FROM_EMAIL", 'contact@yourbest.com');
defined('PUSH_NOTIFICATION_SERVER_KEY') or define("PUSH_NOTIFICATION_SERVER_KEY", 'AAAACbFuV00:APA91bH_y-RSoABIbneCm9EnQ_waZFBxhsey3uUjqFA1UH3EHk_6aALY3Dnt7yGL6K23ATP5LgPN5kOPMxlr1FVUtoWViHC7cl7lT_0pvoTTLIHs9gtjS3hjoS0ne9BYtJoHkicNGH3p');

defined('IMAGE_UPLOAD_PATH') or define("IMAGE_UPLOAD_PATH", public_path() . '/uploads/profile/');
defined('AVATAR_UPLOAD_PATH') or define("AVATAR_UPLOAD_PATH", public_path() . '/uploads/avatars/');
defined('MEDIA_UPLOAD_PATH') or define("MEDIA_UPLOAD_PATH", public_path() . '/uploads/Media/');
defined('STICKERS_UPLOAD_PATH') or define("STICKERS_UPLOAD_PATH", public_path() . '/uploads/stickers/');

defined('THUMBNAIL_UPLOAD_PATH') or define("THUMBNAIL_UPLOAD_PATH", public_path() . '/uploads/thumbnail/');
defined('CATEGORY_IMAGE_PATH') or define("CATEGORY_IMAGE_PATH", public_path() . '/uploads/category/');
defined('SUB_CATEGORY_IMAGE_PATH') or define("SUB_CATEGORY_IMAGE_PATH", public_path() . '/uploads/sub_category/');

defined('SOCIAL_PASS') or define("SOCIAL_PASS", 'y5sUdwLH7*STamnnjRu21!11I269Uw8Ijk%^xlLRN');
defined('OTHER_ACCOUNT_PASS') or define("OTHER_ACCOUNT_PASS", 'y5sUdwLH7*STamn1I269Uw8Ijk%^xlLRN');

defined('STATUS_BAD_REQUEST') or define("STATUS_BAD_REQUEST", 400);
defined('STATUS_UNAUTHORIZED') or define("STATUS_UNAUTHORIZED", 401);
defined('STATUS_CREATED') or define("STATUS_CREATED", 201);
defined('STATUS_OK') or define("STATUS_OK", 200);
defined('STATUS_GENERAL_ERROR') or define("STATUS_GENERAL_ERROR", 500);
defined('STATUS_FORBIDDEN') or define("STATUS_FORBIDDEN", 403);
defined('STATUS_NOT_FOUND') or define("STATUS_NOT_FOUND", 404);
defined('STATUS_METHOD_NOT_ALLOWED') or define("STATUS_METHOD_NOT_ALLOWED", 405);
defined('STATUS_ALREADY_EXIST') or define("STATUS_ALREADY_EXIST", 409);
defined('UNPROCESSABLE_ENTITY') or define("UNPROCESSABLE_ENTITY", 422);

defined('POST_LIKE') or define("POST_LIKE", 'POST_LIKE');
defined('POST_COMMENT') or define("POST_COMMENT", 'POST_COMMENT');
defined('MESSAGE_RECEIVED') or define("MESSAGE_RECEIVED", 'MESSAGE_RECEIVED');
defined('FOLLOW_REQUEST') or define("FOLLOW_REQUEST", 'FOLLOW_REQUEST');
defined('JOIN_REQUEST') or define("JOIN_REQUEST", 'JOIN_REQUEST');
defined('GROUP_INVITE') or define("GROUP_INVITE", 'GROUP_INVITE');
defined('ACCEPT_JOIN_REQUEST') or define("ACCEPT_JOIN_REQUEST", 'ACCEPT_JOIN_REQUEST');

defined('DEFAULT_ERROR_MESSAGE') or define("DEFAULT_ERROR_MESSAGE", "Oops! some error occured, please try again");

defined('USER_TYPES') or define("USER_TYPES", [
    "user", "admin", "employer"
]);

defined('APP_TIMEZONE') or define("APP_TIMEZONE", 'Asia/Kolkata');

defined('WEEKEND_DAYS') or define("WEEKEND_DAYS", [

    '0' => 'Sunday',
    '1' => 'Monday',
    '2' => 'Tuesday',
    '3' => 'Wednesday',
    '4' => 'Thursday',
    '5' => 'Friday',
    '6' => 'Saturday',
]);

defined('SERVICE_STATUS') or define("SERVICE_STATUS", [
    '1' => 'applied', //Pending for provider
    '2' => 'accepted', //Make Payment for user
    '3' => 'declined',
    '4' => 'rescheduled', //when client rescheduled
    '5' => 'canceled', //when client canceled
    '6' => 'On the way', //when Work Completed and payment done by user
    '7' => 'Complete',
    '8' => 'Review', //when provider cancelled
]);


defined('BOOKING_ACCEPTED') or define("BOOKING_ACCEPTED", "BOOKING_ACCEPTED");
defined('BOOKING_REJECTED') or define("BOOKING_REJECTED", "BOOKING_REJECTED");
defined('BOOKING_RESCHEDULED') or define("BOOKING_RESCHEDULED", "BOOKING_RESCHEDULED");
defined('BOOKING_CANCELED') or define("BOOKING_CANCELED", "BOOKING_CANCELED");

defined('USER_GET_LIST') or define("USER_GET_LIST", "USER_GET_LIST");


defined('ACTIVE') or define("ACTIVE", "ACTIVE");
defined('INACTIVE') or define("INACTIVE", "INACTIVE");


defined('ACCEPTED_NOTIFICATION_MESSAGE') or define("ACCEPTED_NOTIFICATION_MESSAGE", "Your offer has been accepted");
defined('REJECTED_NOTIFICATION_MESSAGE') or define("REJECTED_NOTIFICATION_MESSAGE", "Your offer has been rejected");
defined('ON_THE_WAY_NOTIFICATION_MESSAGE') or define("ON_THE_WAY_NOTIFICATION_MESSAGE", "on the way to start ");
defined('WORKING_ON_NOTIFICATION_MESSAGE') or define("WORKING_ON_NOTIFICATION_MESSAGE", "started working on ");
defined('COMPLETED_NOTIFICATION_MESSAGE') or define("COMPLETED_NOTIFICATION_MESSAGE", "Your job has been completed");

defined('MONTH_ARR') or define("MONTH_ARR", [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
]);

defined('MONTH_ARR_NUMBER') or define("MONTH_ARR_NUMBER", [
    '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
]);

defined('MOBILE_COUNTRY_CODE') or define("MOBILE_COUNTRY_CODE", [
    '213' => 'Algeria (+213)',
    '376' => 'Andorra (+376)',
    '244' => 'Angola (+244)',
    '1264' => 'Anguilla (+1264)',
    '1268' => 'Antigua & Barbuda (+1268)',
    '54' => 'Argentina (+54)',
    '374' => 'Armenia (+374)',
    '297' => 'Aruba (+297)',
    '61' => 'Australia (+61)',
    '43' => 'Austria (+43)',
    '994' => 'Azerbaijan (+994)',
    '1242' => 'Bahamas (+1242)',
    '973' => 'Bahrain (+973)',
    '880' => 'Bangladesh (+880)',
    '1246' => 'Barbados (+1246)',
    '375' => 'Belarus (+375)',
    '32' => 'Belgium (+32)',
    '501' => 'Belize (+501)',
    '229' => 'Benin (+229)',
    '1441' => 'Bermuda (+1441)',
    '975' => 'Bhutan (+975)',
    '591' => 'Bolivia (+591)',
    '387' => 'Bosnia Herzegovina (+387)',
    '267' => 'Botswana (+267)',
    '55' => 'Brazil (+55)',
    '673' => 'Brunei (+673)',
    '359' => 'Bulgaria (+359)',
    '226' => 'Burkina Faso (+226)',
    '257' => 'Burundi (+257)',
    '855' => 'Cambodia (+855)',
    '237' => 'Cameroon (+237)',
    '1' => 'Canada (+1)',
    '238' => 'Cape Verde Islands (+238)',
    '1345' => 'Cayman Islands (+1345)',
    '236' => 'Central African Republic (+236)',
    '56' => 'Chile (+56)',
    '86' => 'China (+86)',
    '57' => 'Colombia (+57)',
    '269' => 'Comoros (+269)',
    '242' => 'Congo (+242)',
    '682' => 'Cook Islands (+682)',
    '506' => 'Costa Rica (+506)',
    '385' => 'Croatia (+385)',
    '53' => 'Cuba (+53)',
    '90392' => 'Cyprus North (+90392)',
    '357' => 'Cyprus South (+357)',
    '42' => 'Czech Republic (+42)',
    '45' => 'Denmark (+45)',
    '253' => 'Djibouti (+253)',
    '1809' => 'Dominica (+1809)',
    '1809' => 'Dominican Republic (+1809)',
    '593' => 'Ecuador (+593)',
    '20' => 'Egypt (+20)',
    '503' => 'El Salvador (+503)',
    '240' => 'Equatorial Guinea (+240)',
    '291' => 'Eritrea (+291)',
    '372' => 'Estonia (+372)',
    '251' => 'Ethiopia (+251)',
    '500' => 'Falkland Islands (+500)',
    '298' => 'Faroe Islands (+298)',
    '679' => 'Fiji (+679)',
    '358' => 'Finland (+358)',
    '33' => 'France (+33)',
    '594' => 'French Guiana (+594)',
    '689' => 'French Polynesia (+689)',
    '241' => 'Gabon (+241)',
    '220' => 'Gambia (+220)',
    '7880' => 'Georgia (+7880)',
    '49' => 'Germany (+49)',
    '233' => 'Ghana (+233)',
    '350' => 'Gibraltar (+350)',
    '30' => 'Greece (+30)',
    '299' => 'Greenland (+299)',
    '1473' => 'Grenada (+1473)',
    '590' => 'Guadeloupe (+590)',
    '671' => 'Guam (+671)',
    '502' => 'Guatemala (+502)',
    '224' => 'Guinea (+224)',
    '245' => 'Guinea - Bissau (+245)',
    '592' => 'Guyana (+592)',
    '509' => 'Haiti (+509)',
    '504' => 'Honduras (+504)',
    '852' => 'Hong Kong (+852)',
    '36' => 'Hungary (+36)',
    '354' => 'Iceland (+354)',
    '91' => 'India (+91)',
    '62' => 'Indonesia (+62)',
    '98' => 'Iran (+98)',
    '964' => 'Iraq (+964)',
    '353' => 'Ireland (+353)',
    '972' => 'Israel (+972)',
    '39' => 'Italy (+39)',
    '1876' => 'Jamaica (+1876)',
    '81' => 'Japan (+81)',
    '962' => 'Jordan (+962)',
    '7' => 'Kazakhstan (+7)',
    '254' => 'Kenya (+254)',
    '686' => 'Kiribati (+686)',
    '850' => 'Korea North (+850)',
    '82' => 'Korea South (+82)',
    '965' => 'Kuwait (+965)',
    '996' => 'Kyrgyzstan (+996)',
    '856' => 'Laos (+856)',
    '371' => 'Latvia (+371)',
    '961' => 'Lebanon (+961)',
    '266' => 'Lesotho (+266)',
    '231' => 'Liberia (+231)',
    '218' => 'Libya (+218)',
    '417' => 'Liechtenstein (+417)',
    '370' => 'Lithuania (+370)',
    '352' => 'Luxembourg (+352)',
    '853' => 'Macao (+853)',
    '389' => 'Macedonia (+389)',
    '261' => 'Madagascar (+261)',
    '265' => 'Malawi (+265)',
    '60' => 'Malaysia (+60)',
    '960' => 'Maldives (+960)',
    '223' => 'Mali (+223)',
    '356' => 'Malta (+356)',
    '692' => 'Marshall Islands (+692)',
    '596' => 'Martinique (+596)',
    '222' => 'Mauritania (+222)',
    '269' => 'Mayotte (+269)',
    '52' => 'Mexico (+52)',
    '691' => 'Micronesia (+691)',
    '373' => 'Moldova (+373)',
    '377' => 'Monaco (+377)',
    '976' => 'Mongolia (+976)',
    '1664' => 'Montserrat (+1664)',
    '212' => 'Morocco (+212)',
    '258' => 'Mozambique (+258)',
    '95' => 'Myanmar (+95)',
    '264' => 'Namibia (+264)',
    '674' => 'Nauru (+674)',
    '977' => 'Nepal (+977)',
    '31' => 'Netherlands (+31)',
    '687' => 'New Caledonia (+687)',
    '64' => 'New Zealand (+64)',
    '505' => 'Nicaragua (+505)',
    '227' => 'Niger (+227)',
    '234' => 'Nigeria (+234)',
    '683' => 'Niue (+683)',
    '672' => 'Norfolk Islands (+672)',
    '670' => 'Northern Marianas (+670)',
    '47' => 'Norway (+47)',
    '968' => 'Oman (+968)',
    '680' => 'Palau (+680)',
    '507' => 'Panama (+507)',
    '675' => 'Papua New Guinea (+675)',
    '595' => 'Paraguay (+595)',
    '51' => 'Peru (+51)',
    '63' => 'Philippines (+63)',
    '48' => 'Poland (+48)',
    '351' => 'Portugal (+351)',
    '1787' => 'Puerto Rico (+1787)',
    '974' => 'Qatar (+974)',
    '262' => 'Reunion (+262)',
    '40' => 'Romania (+40)',
    '7' => 'Russia (+7)',
    '250' => 'Rwanda (+250)',
    '378' => 'San Marino (+378)',
    '239' => 'Sao Tome & Principe (+239)',
    '966' => 'Saudi Arabia (+966)',
    '221' => 'Senegal (+221)',
    '381' => 'Serbia (+381)',
    '248' => 'Seychelles (+248)',
    '232' => 'Sierra Leone (+232)',
    '65' => 'Singapore (+65)',
    '421' => 'Slovak Republic (+421)',
    '386' => 'Slovenia (+386)',
    '677' => 'Solomon Islands (+677)',
    '252' => 'Somalia (+252)',
    '27' => 'South Africa (+27)',
    '34' => 'Spain (+34)',
    '94' => 'Sri Lanka (+94)',
    '290' => 'St. Helena (+290)',
    '1869' => 'St. Kitts (+1869)',
    '1758' => 'St. Lucia (+1758)',
    '249' => 'Sudan (+249)',
    '597' => 'Suriname (+597)',
    '268' => 'Swaziland (+268)',
    '46' => 'Sweden (+46)',
    '41' => 'Switzerland (+41)',
    '963' => 'Syria (+963)',
    '886' => 'Taiwan (+886)',
    '7' => 'Tajikstan (+7)',
    '66' => 'Thailand (+66)',
    '228' => 'Togo (+228)',
    '676' => 'Tonga (+676)',
    '1868' => 'Trinidad & Tobago (+1868)',
    '216' => 'Tunisia (+216)',
    '90' => 'Turkey (+90)',
    '7' => 'Turkmenistan (+7)',
    '993' => 'Turkmenistan (+993)',
    '1649' => 'Turks & Caicos Islands (+1649)',
    '688' => 'Tuvalu (+688)',
    '256' => 'Uganda (+256)',
    '380' => 'Ukraine (+380)',
    '971' => 'United Arab Emirates (+971)',
    '598' => 'Uruguay (+598)',
    '7' => 'Uzbekistan (+7)',
    '678' => 'Vanuatu (+678)',
    '379' => 'Vatican City (+379)',
    '58' => 'Venezuela (+58)',
    '84' => 'Vietnam (+84)',
    '84' => 'Virgin Islands - British (+1284)',
    '84' => 'Virgin Islands - US (+1340)',
    '681' => 'Wallis & Futuna (+681)',
    '969' => 'Yemen (North)(+969)',
    '967' => 'Yemen (South)(+967)',
    '260' => 'Zambia (+260)',
    '263' => 'Zimbabwe (+263)',
    '44' => 'UK (+44)',
    '1' => 'USA (+1)',
]);
