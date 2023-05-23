require('dotenv').config();
const app = require('express')();
const server = require('http').createServer(app);
const io = require('socket.io')(server);
const axios = require('axios');
var moment = require('moment-timezone');
var fs = require( 'fs' );
const { BACKEND_BASE_URL } = require('./api');
const { MATCH_SPORTS } = require('./api');
const { GET_FT_DATA} = require('./api');
const { GET_IN_PLAY_DATA } = require('./api');
const { GET_IN_PLAY_SCORE } = require('./api');
var { getUID_VER } = require('./controllers/reload.controller');
var { dispatchUID_VER } = require('./controllers/reload.controller');
var { getSeverUrl } = require('./controllers/serverUrl.controller');
var { getFT_FU_R_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORNER_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_CORRECT_SCORE_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_FU_R_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_HDP_OU_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_CORNER_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_DEFAULT_TODAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_DEFAULT_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORNER_TODAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_CORNER_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_EARLY } = require('./controllers/third_party_ft.controller');
var { getFT_DEFAULT_EARLY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_EARLY }  = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_CHAMPION } = require('./controllers/third_party_ft.controller');
var { getFT_MAIN_CHAMPION } = require('./controllers/third_party_ft.controller');
var { dispatchFT_MAIN_CHAMPION } = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_DEFAULT_PARLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_DEFAULT_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_MAIN_FAVORITE } = require('./controllers/third_party_ft.controller');
var { dispatchFT_MAIN_FAVORITE } = require('./controllers/third_party_ft.controller');
var { dispatchFT_MAIN_FAVORITE } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_FAVORITE } = require('./controllers/third_party_ft.controller');
var { getFT_MORE_TODAY } = require('./controllers/third_party_ft.controller');

// =============== BK Controller ======================== //
var { getBK_MAIN_INPLAY } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_INPLAY } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_TODAY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_TODAY } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_TODAY } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_EARLY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_EARLY } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_CHAMPION } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_CHAMPION } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_CHAMPION } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_PARLAY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_PARLAY } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_PARLAY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_FAVORITE } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_FAVORITE} = require('./controllers/third_party_bk.controller');
var { getBK_MORE_TODAY } = require('./controllers/third_party_bk.controller');
var { getBK_MORE_INPLAY } = require('./controllers/third_party_bk.controller');
var { getBK_MORE_PARLAY } = require('./controllers/third_party_bk.controller');

// =========================== Sports API Reload ===================== //
var { getSystemTime } = require('./controllers/third_party_reload.controller');

// ====================== get Score Result ===================== //
var { getFTScore } = require('./controllers/third_party_score.controller');
var { getBKScore } = require('./controllers/third_party_score.controller');
var { getOtherFTScore } = require('./controllers/third_party_score.controller');
var { getOtherBKScore } = require('./controllers/third_party_score.controller');

var { dispatchFT_AUTO_CHECK_SCORE } = require('./controllers/check_score.controller');
var { dispatchBK_AUTO_CHECK_SCORE } = require('./controllers/check_score.controller');
var { dispatchFT_PARLAY_AUTO_CHECK_SCORE } = require('./controllers/check_score.controller');
var { dispatchBK_PARLAY_AUTO_CHECK_SCORE } = require('./controllers/check_score.controller');

// ============================= Lottery Six Mark ======================== //
var { dispatchGetLotteryStatus } = require('./controllers/kakithe.controller');
var { dispatchUpdateLotteryHandicap } = require('./controllers/kakithe.controller');
var { dispatchGetMacaoLotteryStatus } = require('./controllers/kakithe.controller');
var { dispatchUpdateMacaoLotteryHandicap } = require('./controllers/kakithe.controller');

// ============================= Lottery Always Color ======================== //
var { dispatchGetLotteryResult } = require('./controllers/lottery_result.controller');
var { dispatchGetLotteryResultTCPL3 } = require('./controllers/lottery_result.controller');
var { dispatchGetLotteryResultFC3D } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutAZXY10 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutAZXY5 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutBJKN } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutBJPK } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutCQ } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutCQSF } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutD3 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutFFC5 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutGD11 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutGDSF } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutGXSF } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutJX } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutP3 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutT3 } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutTJ } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutTJSF } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutTWSSC } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutTXSSC } = require('./controllers/lottery_result.controller');
var { dispatchCheckoutXYFT } = require('./controllers/lottery_result.controller');

var obtIterval = 0;
var correctScoreInterval = 0;
var ftInPlayInterval = 0;
var leagueTodayInterval = 0;
var leagueEarlyInterval = 0;
var ftTodayInterval = 0;
var ftEarlyInterval = 0;
var leagueChampionInterval = 0;
var ftChampionInterval = 0;
var leagueParlayInterval = 0;
var ftParlayInterval = 0;
var ftFavoriteInterval = 0;
var scoreResultInterval = 0;
var uidInterval = 0;
var bkInplayInterval = 0;
var leagueBKTodayInterval = 0;
var bkTodayInterval = 0;
var bkEarlyInterval = 0;
var leagueBKEarlyInterval = 0;
var leagueBKChampionInterval = 0;
var bkChampionInterval = 0;
var leagueBKParlayInterval = 0;
var bkParlayInterval = 0;
var bkFavoriteInterval = 0;
var scoreBKResultInterval = 0;

var ftInPlayList = null;
var bkInPlayList = null;

var ft_today_count = 0;
var ft_inplay_count = 0;
var ft_early_count = 0;
var ft_champion_count = 0;
var ft_popular_count = 0;
var bk_today_count = 0;
var bk_inplay_count = 0;
var bk_early_count = 0;
var bk_champion_count = 0;
var bk_popular_count = 0;
var total_today_count = 0;
var total_inplay_count = 0;
var total_early_count = 0;
var total_champion_count = 0;
var total_popular_count = 0;
var total_count = 0;
var ft_total_count = 0;
var bk_total_count = 0;

// const userName = process.env.USER_NAME;
// const passWord = process.env.USER_KEY;
// const thirdPartyBaseUrl = "https://www.hga030.com";

var thirdPartyAuthData = {
    uid: "gjzqzk44m30901985l126991b0",
    version: "-3ed5-bug4-0309-95881ae5676be2",
    thirdPartyBaseUrl: "https://www.hga030.com"
}

const thirdPartyScoreBaseUrl = ['http://zq0666.com','http://www.zq0666.com'];
const thirdPartyScoreUrl = thirdPartyScoreBaseUrl[0] + '/app/member/score.php?type=';

const thirdPartyLotteryBaseUrl = ['http://ssc.zq0666.com','http://ssc2.zq0666.com'];
const thirdPartyLotteryResultUrl = thirdPartyLotteryBaseUrl[1] + '/result_new.php';
const thirdPartyOtherLotteryResultUrl = 'http://vip.manycai.com/K25feb33135b773';

const userArray = ["4056hg", "4057hg", "4058hg", "4059hg", "4060hg"];
const passWord = "wang8899";
const thirdPartyBaseUrlArray = ["https://hga026.com", "https://hga030.com", "https://hga035.com", "https://www.hga038.com", "https://m588.hga030.com", "https://m739.hga030.com"];


// setInterval( async () => {
//     await dispatchFT_AUTO_CHECK_SCORE();
//     await dispatchBK_AUTO_CHECK_SCORE();
//     await dispatchFT_PARLAY_AUTO_CHECK_SCORE();
//     await dispatchBK_PARLAY_AUTO_CHECK_SCORE();
// }, 20000)


// setInterval(async () => {
//     let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
//     if (result["msg"] == "doubleLogin" || result["code"] == "error") {
//         let userName = userArray[Math.floor(Math.random() * 5)];
//         let thirdPartyBaseUrl = thirdPartyBaseUrlArray[Math.floor(Math.random() * 6)];
//         let uid = await getUID_VER(userName, passWord, thirdPartyBaseUrl);
//         console.log("uid================", uid);
//         if (uid != "" && uid != undefined) {
//             thirdPartyAuthData["uid"] = uid;
//             thirdPartyAuthData["thirdPartyBaseUrl"] = thirdPartyBaseUrl;
//         }
//     }
// }, 10000);


// setTimeout(async () => {
//     let userName = userArray[Math.floor(Math.random() * 5)];
//     let thirdPartyBaseUrl = thirdPartyBaseUrlArray[Math.floor(Math.random() * 6)];
//     let uid = await getUID_VER(userName, passWord, thirdPartyBaseUrl);
//     console.log("uid================", uid);
//     if (uid != "" && uid != undefined) {
//         thirdPartyAuthData["uid"] = uid;
//         thirdPartyAuthData["thirdPartyBaseUrl"] = thirdPartyBaseUrl;
//     }
// }, 1000);



// ftInPlayInterval = setInterval(async () => {
//     ftInPlayList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
//     // console.log(ftInPlayList);
//     if (ftInPlayList && ftInPlayList.length > 0) {
//         ft_inplay_count = ftInPlayList.length;
//         await Promise.all(ftInPlayList.map(async item => {

//             if (item["HDP_OU"] === 1) {

//                 let response = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"]});
//                 // console.log("HDP_OU Result: ", response)

//                 if (response != undefined && response != null) {

//                     item["M_LetB_RB_1"] = response["M_LetB_RB_1"] == undefined ? "" : response["M_LetB_RB_1"];
//                     item["MB_LetB_Rate_RB_1"] = response["MB_LetB_Rate_RB_1"] == undefined ? 0 : response["MB_LetB_Rate_RB_1"];
//                     item["TG_LetB_Rate_RB_1"] = response["TG_LetB_Rate_RB_1"] == undefined ? 0 : response["TG_LetB_Rate_RB_1"];
//                     item["MB_Dime_RB_1"] = response["MB_Dime_RB_1"] == undefined ? "" : response["MB_Dime_RB_1"];
//                     item["TG_Dime_RB_1"] = response["TG_Dime_RB_1"] == undefined ? "" : response["TG_Dime_RB_1"];
//                     item["MB_Dime_Rate_RB_1"] = response["MB_Dime_Rate_RB_1"] == undefined ? 0 : response["MB_Dime_Rate_RB_1"];
//                     item["TG_Dime_Rate_RB_1"] = response["TG_Dime_Rate_RB_1"] == undefined ? 0 : response["TG_Dime_Rate_RB_1"];
//                     item["M_LetB_RB_2"] = response["M_LetB_RB_2"] == undefined ? "" : response["M_LetB_RB_2"];
//                     item["MB_LetB_Rate_RB_2"] = response["MB_LetB_Rate_RB_2"] == undefined ? 0 : response["MB_LetB_Rate_RB_2"];
//                     item["TG_LetB_Rate_RB_2"] = response["TG_LetB_Rate_RB_2"] == undefined ? 0 : response["TG_LetB_Rate_RB_2"];
//                     item["MB_Dime_RB_2"] = response["MB_Dime_RB_2"] == undefined ? "" : response["MB_Dime_RB_2"];
//                     item["TG_Dime_RB_2"] = response["TG_Dime_RB_2"] == undefined ? "" : response["TG_Dime_RB_2"];
//                     item["MB_Dime_Rate_RB_2"] = response["MB_Dime_Rate_RB_2"] == undefined ? 0 : response["MB_Dime_Rate_RB_2"];
//                     item["TG_Dime_Rate_RB_2"] = response["TG_Dime_Rate_RB_2"] == undefined ? 0 : response["TG_Dime_Rate_RB_2"];
//                     item["M_LetB_RB_3"] = response["M_LetB_RB_3"] == undefined ? "" : response["M_LetB_RB_3"];
//                     item["MB_LetB_Rate_RB_3"] = response["MB_LetB_Rate_RB_3"] == undefined ? 0 : response["MB_LetB_Rate_RB_3"];
//                     item["TG_LetB_Rate_RB_3"] = response["TG_LetB_Rate_RB_3"] == undefined ? 0 : response["TG_LetB_Rate_RB_3"];
//                     item["MB_Dime_RB_3"] = response["MB_Dime_RB_3"] == undefined ? "" : response["MB_Dime_RB_3"];
//                     item["TG_Dime_RB_3"] = response["TG_Dime_RB_3"] == undefined ? "" : response["TG_Dime_RB_3"];
//                     item["MB_Dime_Rate_RB_3"] = response["MB_Dime_Rate_RB_3"] == undefined ? 0 : response["MB_Dime_Rate_RB_3"];
//                     item["TG_Dime_Rate_RB_3"] = response["TG_Dime_Rate_RB_3"] == undefined ? 0 : response["TG_Dime_Rate_RB_3"];

//                 } else {

//                     item["M_LetB_RB_1"] = "";
//                     item["MB_LetB_Rate_RB_1"] = 0;
//                     item["TG_LetB_Rate_RB_1"] = 0;
//                     item["MB_Dime_RB_1"] = "";
//                     item["TG_Dime_RB_1"] = "";
//                     item["MB_Dime_Rate_RB_1"] = 0;
//                     item["TG_Dime_Rate_RB_1"] = 0;
//                     item["M_LetB_RB_2"] = "";
//                     item["MB_LetB_Rate_RB_2"] = 0;
//                     item["TG_LetB_Rate_RB_2"] = 0;
//                     item["MB_Dime_RB_2"] = "";
//                     item["TG_Dime_RB_2"] = "";
//                     item["MB_Dime_Rate_RB_2"] = 0;
//                     item["TG_Dime_Rate_RB_2"] = 0;
//                     item["M_LetB_RB_3"] = "";
//                     item["MB_LetB_Rate_RB_3"] = 0;
//                     item["TG_LetB_Rate_RB_3"] = 0;
//                     item["MB_Dime_RB_3"] = "";
//                     item["TG_Dime_RB_3"] = "";
//                     item["MB_Dime_Rate_RB_3"] = 0;
//                     item["TG_Dime_Rate_RB_3"] = 0;

//                 }

                
//             }


//             if (item["CORNER"] === 1) {

//                 let response = await getFT_CORNER_INPLAY(thirdPartyAuthData, {ecid: item["ECID"]});

//                 // console.log(response);

//                 if (response != undefined) {

//                     await dispatchFT_CORNER_INPLAY(response);

//                     item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];
//                     item["MB_Dime_RB_CN"] = response["MB_Dime_RB"] == undefined ? "" : response["MB_Dime_RB"];
//                     item["TG_Dime_RB_CN"] = response["TG_Dime_RB"] == undefined ? "" : response["TG_Dime_RB"];
//                     item["MB_Dime_Rate_RB_CN"] = response["MB_Dime_Rate_RB"] == undefined ? 0 : response["MB_Dime_Rate_RB"];
//                     item["TG_Dime_Rate_RB_CN"] = response["TG_Dime_Rate_RB"] == undefined ? 0 : response["TG_Dime_Rate_RB"];
//                     item["MB_Dime_RB_H_CN"] = response["MB_Dime_RB_H"] == undefined ? "" : response["MB_Dime_RB_H"];
//                     item["TG_Dime_RB_H_CN"] = response["TG_Dime_RB_H"] == undefined ? "" : response["TG_Dime_RB_H"];
//                     item["MB_Dime_Rate_RB_H_CN"] = response["MB_Dime_Rate_RB_H"] == undefined ? 0 : response["MB_Dime_Rate_RB_H"];
//                     item["TG_Dime_Rate_RB_H_CN"] = response["TG_Dime_Rate_RB_H"] == undefined ? 0 : response["TG_Dime_Rate_RB_H"];
//                     item["S_Single_Rate_CN"] = response["S_Single_Rate"] == undefined ? 0 : response["S_Single_Rate"];
//                     item["S_Double_Rate_CN"] = response["S_Double_Rate"] == undefined ? 0 : response["S_Double_Rate"];
//                     item["S_Single_Rate_H_CN"] = response["S_Single_Rate_H"] == undefined ? 0 : response["S_Single_Rate_H"];
//                     item["S_Double_Rate_H_CN"] = response["S_Double_Rate_H"] == undefined ? 0 : response["S_Double_Rate_H"];
//                     item["MB_Ball_CN"] = response["MB_Ball"] == undefined ? 0 : response["MB_Ball"];
//                     item["TG_Ball_CN"] = response["TG_Ball"] == undefined ? 0 : response["TG_Ball"];

//                 } else {

//                     item["CN_MID"] = response["MID"];
//                     item["MB_Dime_RB_CN"] = "";
//                     item["TG_Dime_RB_CN"] = "";
//                     item["MB_Dime_Rate_RB_CN"] = 0;
//                     item["TG_Dime_Rate_RB_CN"] = 0;
//                     item["MB_Dime_RB_H_CN"] = "";
//                     item["TG_Dime_RB_H_CN"] = "";
//                     item["MB_Dime_Rate_RB_H_CN"] = 0;
//                     item["TG_Dime_Rate_RB_H_CN"] = 0;
//                     item["S_Single_Rate_CN"] = 0;
//                     item["S_Double_Rate_CN"] = 0;
//                     item["S_Single_Rate_H_CN"] = 0;
//                     item["S_Double_Rate_H_CN"] = 0;
//                     item["MB_Ball_CN"] = 0;
//                     item["TG_Ball_CN"] = 0

//                 }
//             }

//         }));

//         await dispatchFT_FU_R_INPLAY(ftInPlayList);

//     }

//     io.emit("receivedFTInPlayData", ftInPlayList);

// }, 30000);



// bkInplayInterval = setInterval(async () => {

//     bkInPlayList = await getBK_MAIN_INPLAY(thirdPartyAuthData);

//     // console.log(bkInPlayList);

//     if (bkInPlayList && bkInPlayList.length > 0) {

//         await Promise.all(bkInPlayList.map(async item => {

//             if (item["LID"] !== "" && item["MID"] !== "") {

//                 let moreData = await getBK_MORE_INPLAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "live", flag_class: item["FLAG_CLASS"]});

//                 // console.log(moreData);

//                 moreData.map(moreItem => {
//                     if (item["MID"] === moreItem["MID"]) {
//                         item["S_Single_Rate"] = moreItem["S_Single_Rate"];
//                         item["S_Double_Rate"] = moreItem["S_Double_Rate"];
//                     }
//                 })

//                 // if (moreData != undefined && moreData.length > 0) {

//                 //     bkInPlayList = [...bkInPlayList, ...moreData];

//                 // }

//             }

//         }));

//         bk_inplay_count = bkInPlayList.length;

//         await dispatchBK_MAIN_INPLAY(bkInPlayList) 

//     }

// }, 30000);



// scoreResultInterval = setInterval( async () => {
//     // getFTScore(thirdPartyScoreUrl);
//     getBKScore(thirdPartyScoreUrl);
//     getOtherFTScore(thirdPartyAuthData);
//     getOtherBKScore(thirdPartyAuthData);
// }, 50000);


// setInterval(async () => {

//     ft_today_count = 0;
//     ft_inplay_count = 0;
//     ft_early_count = 0;
//     ft_champion_count = 0;
//     ft_popular_count = 0;
//     bk_today_count = 0;
//     bk_inplay_count = 0;
//     bk_early_count = 0;
//     bk_champion_count = 0;
//     bk_popular_count = 0;
//     total_today_count = 0;
//     total_inplay_count = 0;
//     total_early_count = 0;
//     total_champion_count = 0;
//     total_popular_count = 0;
//     total_count = 0;
//     ft_total_count = 0;
//     bk_total_count = 0;

//     let ftTodayLeagueResult = await getFT_LEAGUE_TODAY(thirdPartyAuthData);

//     if (ftTodayLeagueResult["coupons"] != undefined) {

//         if (Array.isArray(ftTodayLeagueResult["coupons"]["coupon"])) {
//             ftTodayLeagueResult["coupons"]["coupon"].map(item => {
//                 ft_popular_count += (item.lid + "").split(",").length
//                 ft_today_count += (item.lid + "").split(",").length
//             })
//         } else {
//             ft_popular_count += (ftTodayLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//             ft_today_count += (ftTodayLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//         }

//     }

//     if (ftTodayLeagueResult["classifier"] !=undefined) {

//         if (Array.isArray(ftTodayLeagueResult["classifier"]["region"])) {
//             ftTodayLeagueResult["classifier"]["region"].map(regionItem => {
//                 if (Array.isArray(regionItem.league)) {
//                     regionItem.league.map(item => {
//                         ft_today_count += (item.id + "").split(",").length
//                     })
//                 } else {
//                     regionItem["league"].id = regionItem["league"].id + "";
//                     let leagueItem = regionItem["league"];
//                     ft_today_count += (leagueItem.id + "").split(",").length
//                 }
//             })
//         } else {
//             let regionItem = ftTodayLeagueResult["classifier"]["region"];
//             if (Array.isArray(regionItem.league)) {
//                 regionItem.league.map(item => {
//                     item.id = item.id + "";
//                     ft_today_count += (item.id + "").split(",").length
//                 })
//             } else {
//                 regionItem["league"].id = regionItem["league"].id + "";
//                 let leagueItem = regionItem["league"];
//                 ft_today_count += (leagueItem.id + "").split(",").length
//             }
//         }
        
//     }

//     let bkTodayLeagueResult = await getBK_LEAGUE_TODAY(thirdPartyAuthData);

//     // console.log("bkTodayLeagueResult: ", bkTodayLeagueResult);

//     if (bkTodayLeagueResult["coupons"] != undefined) {

//         if (Array.isArray(bkTodayLeagueResult["coupons"]["coupon"])) {
//             bkTodayLeagueResult["coupons"]["coupon"].map(item => {
//                 bk_popular_count += (item.lid + "").split(",").length
//                 bk_today_count += (item.lid + "").split(",").length
//             })
//         } else {
//             bk_popular_count += (bkTodayLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//             bk_today_count += (bkTodayLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//         }

//         // console.log(bk_popular_count, bk_today_count);

//     }

//     if (bkTodayLeagueResult["classifier"] !=undefined) {

//         if (Array.isArray(bkTodayLeagueResult["classifier"]["region"])) {
//             bkTodayLeagueResult["classifier"]["region"].map(regionItem => {
//                 if (Array.isArray(regionItem.league)) {
//                     regionItem.league.map(item => {
//                         bk_today_count += (item.id + "").split(",").length
//                     })
//                 } else {
//                     let leagueItem = regionItem["league"];
//                     bk_today_count += (leagueItem.id + "").split(",").length
//                 }
//             })
//         } else {
//             let regionItem = bkTodayLeagueResult["classifier"]["region"]
//             if (Array.isArray(regionItem.league)) {
//                 regionItem.league.map(item => {
//                     bk_today_count += (item.id + "").split(",").length
//                 })
//             } else {
//                 let leagueItem = regionItem["league"];
//                 bk_today_count += (leagueItem.id + "").split(",").length
//             }
//         }

//     }

//     let ftEarlyLeagueResult = await getFT_LEAGUE_EARLY(thirdPartyAuthData);

//     if (Array.isArray(ftEarlyLeagueResult["coupons"]["coupon"])) {
//         ftEarlyLeagueResult["coupons"]["coupon"].map(item => {
//             ft_popular_count += (item.lid + "").split(",").length
//             ft_early_count += (item.lid + "").split(",").length
//         })
//     } else {
//         ft_popular_count += (ftEarlyLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//         ft_early_count += (ftEarlyLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//     }

//     if (Array.isArray(ftEarlyLeagueResult["classifier"]["region"])) {
//         ftEarlyLeagueResult["classifier"]["region"].map(regionItem => {
//             if (Array.isArray(regionItem.league)) {
//                 regionItem.league.map(item => {
//                     ft_early_count += (item.id + "").split(",").length
//                 })
//             } else {
//                 let leagueItem = regionItem["league"];
//                 ft_early_count += (leagueItem.id + "").split(",").length
//             }
//         })
//     } else {
//         let regionItem = ftEarlyLeagueResult["classifier"]["region"]
//         if (Array.isArray(regionItem.league)) {
//             regionItem.league.map(item => {
//                 ft_early_count += (item.id + "").split(",").length
//             })
//         } else {
//                 let leagueItem = regionItem["league"];
//             ft_early_count += (leagueItem.id + "").split(",").length
//         }
//     }


//     let bkEarlyLeagueResult = await getBK_LEAGUE_EARLY(thirdPartyAuthData);

//     if (bkEarlyLeagueResult["coupons"] != undefined) {

//         if (Array.isArray(bkEarlyLeagueResult["coupons"]["coupon"])) {
//             bkEarlyLeagueResult["coupons"]["coupon"].map(item => {
//                 bk_popular_count += (item.lid + "").split(",").length
//                 bk_early_count += (item.lid + "").split(",").length
//             })
//         } else {
//             bk_popular_count += (bkEarlyLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//             bk_early_count += (bkEarlyLeagueResult["coupons"]["coupon"]["lid"] + "").split(",").length
//         }

//     }

//     if (bkEarlyLeagueResult["classifier"] != undefined) {

//         if (Array.isArray(bkEarlyLeagueResult["classifier"]["region"])) {
//             bkEarlyLeagueResult["classifier"]["region"].map(regionItem => {
//                 if (Array.isArray(regionItem.league)) {
//                     regionItem.league.map(item => {
//                         bk_early_count += (item.id + "").split(",").length
//                     })
//                 } else {
//                     let leagueItem = regionItem["league"];
//                     bk_early_count += (leagueItem.id + "").split(",").length
//                 }
//             })
//         } else {
//             let regionItem = bkEarlyLeagueResult["classifier"]["region"]
//             if (Array.isArray(regionItem.league)) {
//                 regionItem.league.map(item => {
//                     bk_early_count += (item.id + "").split(",").length
//                 })
//             } else {
//                     let leagueItem = regionItem["league"];
//                 bk_early_count += (leagueItem.id + "").split(",").length
//             }
//         }

//     }

//     let ftChampionLeagueResult = await getFT_LEAGUE_CHAMPION(thirdPartyAuthData);

//     if (Array.isArray(ftChampionLeagueResult["classifier"]["region"])) {
//         ftChampionLeagueResult["classifier"]["region"].map(regionItem => {
//             if (Array.isArray(regionItem.league)) {
//                 regionItem.league.map(item => {
//                     ft_champion_count += (item.id + "").split(",").length
//                 })
//             } else {
//                 let leagueItem = regionItem["league"];
//                 ft_champion_count += (leagueItem.id + "").split(",").length
//             }
//         })
//     } else {
//         let regionItem = ftChampionLeagueResult["classifier"]["region"]
//         if (Array.isArray(regionItem.league)) {
//             regionItem.league.map(item => {
//                 ft_champion_count += (item.id + "").split(",").length
//             })
//         } else {
//                 let leagueItem = regionItem["league"];
//             ft_champion_count += (leagueItem.id + "").split(",").length
//         }
//     }

//     let bkChampionLeagueResult = await getBK_LEAGUE_CHAMPION(thirdPartyAuthData);

//     if (bkChampionLeagueResult["classifier"] != undefined) {

//         if (Array.isArray(bkChampionLeagueResult["classifier"]["region"])) {
//             bkChampionLeagueResult["classifier"]["region"].map(regionItem => {
//                 if (Array.isArray(regionItem.league)) {
//                     regionItem.league.map(item => {
//                         bk_champion_count += (item.id + "").split(",").length
//                     })
//                 } else {
//                     let leagueItem = regionItem["league"];
//                     bk_champion_count += (leagueItem.id + "").split(",").length
//                 }
//             })
//         } else {
//             let regionItem = bkChampionLeagueResult["classifier"]["region"]
//             if (Array.isArray(regionItem.league)) {
//                 regionItem.league.map(item => {
//                     bk_champion_count += (item.id + "").split(",").length
//                 })
//             } else {
//                     let leagueItem = regionItem["league"];
//                 bk_champion_count += (leagueItem.id + "").split(",").length
//             }
//         }

//     }

//     ft_total_count = ft_today_count + ft_early_count + ft_champion_count;
//     bk_total_count = bk_today_count + bk_early_count + bk_champion_count;

// }, 60 * 1000);


setInterval(async () => {
    let result = await dispatchGetLotteryStatus();
    if (result == null) {
        return;
    }
    var now = moment().tz("Asia/Hong_Kong").format("YYYY-MM-DD HH:mm:ss");
    console.log(now);
    var close_end = moment(result["zfbdate"]);
    var close_duration = moment.duration(close_end.diff(now));
    var close_time_diff = close_duration.valueOf();
    if (close_time_diff < 0) {
        console.log("close");
        await dispatchUpdateLotteryHandicap({id: result["id"], zfb: 0});
    }
    console.log(result["zfb"], result["best"], close_time_diff);
    if (result["zfb"] == 0 && result["best"] && close_time_diff > 0) {
        console.log("hong kong six mark open:");
        var open_end = moment(result["zfbdate1"]);
        var open_duration = moment.duration(open_end.diff(now));
        var open_time_diff = open_duration.valueOf();
        console.log(open_time_diff);
        if (open_time_diff < 0) {
            await dispatchUpdateLotteryHandicap({id: result["id"], zfb: 1, best: 1});
        }        
    }
}, 60 * 1000);


setInterval(async () => {
    let result = await dispatchGetMacaoLotteryStatus();
    if (result == null) {
        return;
    }
    var now = moment().tz("Asia/Hong_Kong").format("YYYY-MM-DD HH:mm:ss");
    console.log(now);
    var close_end = moment(result["zfbdate"]);
    var close_duration = moment.duration(close_end.diff(now));
    var close_time_diff = close_duration.valueOf();
    if (close_time_diff < 0) {
        console.log("close");
        await dispatchUpdateMacaoLotteryHandicap({id: result["id"], zfb: 0});
    }
    console.log(result["zfb"], result["best"], close_time_diff);
    if (result["zfb"] == 0 && result["best"] && close_time_diff > 0) {
        console.log("open");
        var open_end = moment(result["zfbdate1"]);
        var open_duration = moment.duration(open_end.diff(now));
        var open_time_diff = open_duration.valueOf();
        console.log(open_time_diff);
        if (open_time_diff < 0) {
            await dispatchUpdateMacaoLotteryHandicap({id: result["id"], zfb: 1, best: 1});
        }        
    }
}, 60 * 1000);

setInterval(async () => {
    await dispatchGetLotteryResult(thirdPartyLotteryResultUrl);
    await dispatchGetLotteryResultTCPL3(`${thirdPartyOtherLotteryResultUrl}/TCPL3.json`);
    await dispatchGetLotteryResultFC3D(`${thirdPartyOtherLotteryResultUrl}/FC3D.json`);
    await dispatchCheckoutAZXY5();
    await dispatchCheckoutAZXY10();
    await dispatchCheckoutBJKN();
    await dispatchCheckoutBJPK();
    await dispatchCheckoutCQ();
    await dispatchCheckoutCQSF();
    await dispatchCheckoutD3();
    await dispatchCheckoutGD11();
    await dispatchCheckoutGDSF();
    await dispatchCheckoutGXSF();
    await dispatchCheckoutFFC5();
    await dispatchCheckoutJX();
    await dispatchCheckoutP3();
    await dispatchCheckoutT3();
    await dispatchCheckoutTJ();
    await dispatchCheckoutTJSF();
    await dispatchCheckoutTWSSC();
    await dispatchCheckoutTXSSC();
    await dispatchCheckoutXYFT();
}, 60 * 1000);


var clients = {};


io.on("connection", async function (socket) {

    console.log("socket connected:" + socket.id);

    socket.on('join', function (name) {
        clients[name] = socket.id;
    });

    socket.on('sendUserMoney', (users) => {
        users.map(user => {
            io.to(clients[user.user_name]).emit('receivedMoney', user.current_money);
        })
    })

    // ======================= Sport Count =============================== //

    socket.on('sendInplayCountMessage', async () => {

        // console.log("sendInplayCountMessage");

        let inplay_count_data = {
            total_inplay_count: ft_inplay_count +  bk_inplay_count,
            ft_inplay_count: ft_inplay_count,
            bk_inplay_count: bk_inplay_count
        }

        io.emit("receivedInplayCountMessage", inplay_count_data);

    });

    socket.on('sendTodayCountMessage', () => {

        let today_count_data = {
            total_today_count: ft_today_count +  bk_today_count,
            ft_today_count: ft_today_count,
            bk_today_count: bk_today_count
        }

        io.emit("receivedTodayCountMessage", today_count_data);

    });

    socket.on('sendEarlyCountMessage', () => {

        let early_count_data = {
            total_early_count: ft_early_count +  bk_early_count,
            ft_early_count: ft_early_count,
            bk_early_count: bk_early_count
        }

        io.emit("receivedEarlyCountMessage", early_count_data);

    });

    socket.on('sendChampionCountMessage', () => {

        let champion_count_data = {
            total_champion_count: ft_champion_count +  bk_champion_count,
            ft_champion_count: ft_champion_count,
            bk_champion_count: bk_champion_count
        }

        io.emit("receivedChampionCountMessage", champion_count_data);

    });

    socket.on('sendPopularCountMessage', () => {

        let popular_count_data = {
            total_popular_count: ft_popular_count +  bk_popular_count,
            ft_popular_count: ft_popular_count,
            bk_popular_count: bk_popular_count
        }

        io.emit("receivedPopularCountMessage", popular_count_data);

    });

    socket.on('sendTotalCountMessage', () => {

        let total_count_data = {
            total_count: ft_total_count +  bk_total_count,
            ft_total_count: ft_total_count,
            bk_total_count: bk_total_count
        }

        io.emit("receivedTotalCountMessage", total_count_data);

    });

    socket.on('sendCorrectScoreMessage', async () => {
        // console.log("sendCorrectScoreMessage");
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_INPLAY(thirdPartyAuthData);
        io.emit("receivedFTInPlayScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_INPLAY(thirdPartyAuthData);
            io.emit("receivedFTInPlayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreMessage", () => {
        clearInterval(correctScoreInterval);
    })

    // ========================= FT Inplay Data ========================= //

    socket.on('sendFTInPlayMessage', async () => {

        io.emit("receivedFTInPlayData", ftInPlayList);
    });

    socket.on("stopFT_INPLAY", () => {
        clearInterval(correctScoreInterval);        
        clearInterval(obtIterval);
    })

    // ======================== FT Today Data =========================== //

    socket.on("sendFTTodayMessage", async (data) => {
        let itemList = await getFT_DEFAULT_TODAY(thirdPartyAuthData, data);
        if (itemList && itemList.length > 0) {
            await Promise.all(itemList.map(async item => {
                // console.log(item["MID"])
                if (item["HDP_OU"] === 1) {

                    let response = await getFT_HDP_OU_TODAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "today"});

                    if (response != undefined && response != null) {

                        item["M_LetB_1"] = response["M_LetB_1"] == undefined ? "" : response["M_LetB_1"];
                        item["MB_LetB_Rate_1"] = response["MB_LetB_Rate_1"] == undefined ? 0 : response["MB_LetB_Rate_1"];
                        item["TG_LetB_Rate_1"] = response["TG_LetB_Rate_1"] == undefined ? 0 : response["TG_LetB_Rate_1"];
                        item["MB_Dime_1"] = response["MB_Dime_1"] == undefined ? "" : response["MB_Dime_1"];
                        item["TG_Dime_1"] = response["TG_Dime_1"] == undefined ? "" : response["TG_Dime_1"];
                        item["MB_Dime_Rate_1"] = response["MB_Dime_Rate_1"] == undefined ? 0 : response["MB_Dime_Rate_1"];
                        item["TG_Dime_Rate_1"] = response["TG_Dime_Rate_1"] == undefined ? 0 : response["TG_Dime_Rate_1"];
                        item["M_LetB_2"] = response["M_LetB_2"] == undefined ? "" : response["M_LetB_2"];
                        item["MB_LetB_Rate_2"] = response["MB_LetB_Rate_2"] == undefined ? 0 : response["MB_LetB_Rate_2"];
                        item["TG_LetB_Rate_2"] = response["TG_LetB_Rate_2"] == undefined ? 0 : response["TG_LetB_Rate_2"];
                        item["MB_Dime_2"] = response["MB_Dime_2"] == undefined ? "" : response["MB_Dime_2"];
                        item["TG_Dime_2"] = response["TG_Dime_2"] == undefined ? "" : response["TG_Dime_2"];
                        item["MB_Dime_Rate_2"] = response["MB_Dime_Rate_2"] == undefined ? 0 : response["MB_Dime_Rate_2"];
                        item["TG_Dime_Rate_2"] = response["TG_Dime_Rate_2"] == undefined ? 0 : response["TG_Dime_Rate_2"];
                        item["M_LetB_3"] = response["M_LetB_3"] == undefined ? "" : response["M_LetB_3"];
                        item["MB_LetB_Rate_3"] = response["MB_LetB_Rate_3"] == undefined ? 0 : response["MB_LetB_Rate_3"];
                        item["TG_LetB_Rate_3"] = response["TG_LetB_Rate_3"] == undefined ? 0 : response["TG_LetB_Rate_3"];
                        item["MB_Dime_3"] = response["MB_Dime_3"] == undefined ? "" : response["MB_Dime_3"];
                        item["TG_Dime_3"] = response["TG_Dime_3"] == undefined ? "" : response["TG_Dime_3"];
                        item["MB_Dime_Rate_3"] = response["MB_Dime_Rate_3"] == undefined ? 0 : response["MB_Dime_Rate_3"];
                        item["TG_Dime_Rate_3"] = response["TG_Dime_Rate_3"] == undefined ? 0 : response["TG_Dime_Rate_3"];

                    } else {

                        item["M_LetB_1"] = "";
                        item["MB_LetB_Rate_1"] = 0;
                        item["TG_LetB_Rate_1"] = 0;
                        item["MB_Dime_1"] = "";
                        item["TG_Dime_1"] = "";
                        item["MB_Dime_Rate_1"] = 0;
                        item["TG_Dime_Rate_1"] = 0;
                        item["M_LetB_2"] = "";
                        item["MB_LetB_Rate_2"] = 0;
                        item["TG_LetB_Rate_2"] = 0;
                        item["MB_Dime_2"] = "";
                        item["TG_Dime_2"] = "";
                        item["MB_Dime_Rate_2"] = 0;
                        item["TG_Dime_Rate_2"] = 0;
                        item["M_LetB_3"] = "";
                        item["MB_LetB_Rate_3"] = 0;
                        item["TG_LetB_Rate_3"] = 0;
                        item["MB_Dime_3"] = "";
                        item["TG_Dime_3"] = "";
                        item["MB_Dime_Rate_3"] = 0;
                        item["TG_Dime_Rate_3"] = 0;

                    }
                }


                if (item["CORNER"] == 1) {

                    let response = await getFT_CORNER_TODAY(thirdPartyAuthData, {ecid: item["ECID"]});

                    console.log(response);

                    if (response != undefined && response != null) {

                        await dispatchFT_CORNER_TODAY(response);

                        item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];

                        item["M_LetB_CN"] = response["M_LetB"] == undefined ? "" : response["M_LetB"];
                        item["MB_LetB_Rate_CN"] = response["MB_LetB_Rate"] == undefined ? 0 : response["MB_LetB_Rate"];
                        item["TG_LetB_Rate_CN"] = response["TG_LetB_Rate"] == undefined ? 0 : response["TG_LetB_Rate"];

                        item["MB_Dime_CN"] = response["MB_Dime"] == undefined ? "" : response["MB_Dime"];
                        item["TG_Dime_CN"] = response["TG_Dime"] == undefined ? "" : response["TG_Dime"];
                        item["MB_Dime_Rate_CN"] = response["MB_Dime_Rate"] == undefined ? 0 : response["MB_Dime_Rate"];
                        item["TG_Dime_Rate_CN"] = response["TG_Dime_Rate"] == undefined ? 0 : response["TG_Dime_Rate"];

                        item["M_Flat_Rate_CN"] = response["M_Flat_Rate"] == undefined ? "" : response["M_Flat_Rate"];
                        item["MB_Win_Rate_CN"] = response["MB_Win_Rate"] == undefined ? 0 : response["MB_Win_Rate"];
                        item["TG_Win_Rate_CN"] = response["TG_Win_Rate"] == undefined ? 0 : response["TG_Win_Rate"];

                    } else {                        

                        item["CN_MID"] = "";

                        item["M_LetB_CN"] = "";
                        item["MB_LetB_Rate_CN"] = 0;
                        item["TG_LetB_Rate_CN"] = 0;

                        item["MB_Dime_CN"] = "";
                        item["TG_Dime_CN"] = "";
                        item["MB_Dime_Rate_CN"] = 0;
                        item["TG_Dime_Rate_CN"] = 0;

                        item["M_Flat_Rate_CN"] = 0;
                        item["MB_Win_Rate_CN"] = 0;
                        item["TG_Win_Rate_CN"] = 0;
                    }
                }

                if (item["LID"] !== "" && item["ECID"] !== "") {


                    let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});

                    if (moreData !== undefined && moreData != null && moreData != {}) {
                            
                        item["MBMB"] = moreData["MBMB"] == undefined ? 0 : moreData["MBMB"];
                        item["MBFT"] = moreData["MBFT"] == undefined ? 0 : moreData["MBFT"];
                        item["MBTG"] = moreData["MBTG"] == undefined ? 0 : moreData["MBTG"];
                        item["FTMB"] = moreData["FTMB"] == undefined ? 0 : moreData["FTMB"];
                        item["FTFT"] = moreData["FTFT"] == undefined ? 0 : moreData["FTFT"];
                        item["FTTG"] = moreData["FTTG"] == undefined ? 0 : moreData["FTTG"];
                        item["TGMB"] = moreData["TGMB"] == undefined ? 0 : moreData["TGMB"];
                        item["TGTG"] = moreData["TGTG"] == undefined ? 0 : moreData["TGTG"];
                        item["TGFT"] = moreData["TGFT"] == undefined ? 0 : moreData["TGFT"];
                        item["F_Show"] = moreData["F_Show"] == undefined ? 0 : moreData["F_Show"];

                        item["S_0_1"] = moreData["S_0_1"] == undefined ? 0 : moreData["S_0_1"];
                        item["S_2_3"] = moreData["S_2_3"] == undefined ? 0 : moreData["S_2_3"];
                        item["S_4_6"] = moreData["S_4_6"] == undefined ? 0 : moreData["S_4_6"];
                        item["S_7UP"] = moreData["S_7UP"] == undefined ? 0 : moreData["S_7UP"];
                        item["T_Show"] = moreData["T_Show"] == undefined ? 0 : moreData["T_Show"];

                    } else {
                        item["MBMB"] = 0 ;
                        item["MBFT"] = 0;
                        item["MBTG"] = 0;
                        item["FTMB"] = 0;
                        item["FTFT"] = 0;
                        item["FTTG"] = 0;
                        item["TGMB"] = 0;
                        item["TGTG"] = 0;
                        item["TGFT"] = 0;
                        item["F_Show"] = 0;

                        item["S_0_1"] = 0;
                        item["S_2_3"] = 0;
                        item["S_4_6"] = 0;
                        item["S_7UP"] = 0;
                        item["T_Show"] = 0;
                    }
                }

            }));
            await dispatchFT_DEFAULT_TODAY(itemList);
        }
        io.emit("receivedFTTodayMessage", itemList);
        ftTodayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_TODAY(thirdPartyAuthData, data);
            if (itemList && itemList.length > 0) {
                await Promise.all(itemList.map(async item => {
                    if (item["HDP_OU"] === 1) {

                        let response = await getFT_HDP_OU_TODAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "today"});

                        if (response != undefined && response != null) {

                            item["M_LetB_1"] = response["M_LetB_1"] == undefined ? "" : response["M_LetB_1"];
                            item["MB_LetB_Rate_1"] = response["MB_LetB_Rate_1"] == undefined ? 0 : response["MB_LetB_Rate_1"];
                            item["TG_LetB_Rate_1"] = response["TG_LetB_Rate_1"] == undefined ? 0 : response["TG_LetB_Rate_1"];
                            item["MB_Dime_1"] = response["MB_Dime_1"] == undefined ? "" : response["MB_Dime_1"];
                            item["TG_Dime_1"] = response["TG_Dime_1"] == undefined ? "" : response["TG_Dime_1"];
                            item["MB_Dime_Rate_1"] = response["MB_Dime_Rate_1"] == undefined ? 0 : response["MB_Dime_Rate_1"];
                            item["TG_Dime_Rate_1"] = response["TG_Dime_Rate_1"] == undefined ? 0 : response["TG_Dime_Rate_1"];
                            item["M_LetB_2"] = response["M_LetB_2"] == undefined ? "" : response["M_LetB_2"];
                            item["MB_LetB_Rate_2"] = response["MB_LetB_Rate_2"] == undefined ? 0 : response["MB_LetB_Rate_2"];
                            item["TG_LetB_Rate_2"] = response["TG_LetB_Rate_2"] == undefined ? 0 : response["TG_LetB_Rate_2"];
                            item["MB_Dime_2"] = response["MB_Dime_2"] == undefined ? "" : response["MB_Dime_2"];
                            item["TG_Dime_2"] = response["TG_Dime_2"] == undefined ? "" : response["TG_Dime_2"];
                            item["MB_Dime_Rate_2"] = response["MB_Dime_Rate_2"] == undefined ? 0 : response["MB_Dime_Rate_2"];
                            item["TG_Dime_Rate_2"] = response["TG_Dime_Rate_2"] == undefined ? 0 : response["TG_Dime_Rate_2"];
                            item["M_LetB_3"] = response["M_LetB_3"] == undefined ? "" : response["M_LetB_3"];
                            item["MB_LetB_Rate_3"] = response["MB_LetB_Rate_3"] == undefined ? 0 : response["MB_LetB_Rate_3"];
                            item["TG_LetB_Rate_3"] = response["TG_LetB_Rate_3"] == undefined ? 0 : response["TG_LetB_Rate_3"];
                            item["MB_Dime_3"] = response["MB_Dime_3"] == undefined ? "" : response["MB_Dime_3"];
                            item["TG_Dime_3"] = response["TG_Dime_3"] == undefined ? "" : response["TG_Dime_3"];
                            item["MB_Dime_Rate_3"] = response["MB_Dime_Rate_3"] == undefined ? 0 : response["MB_Dime_Rate_3"];
                            item["TG_Dime_Rate_3"] = response["TG_Dime_Rate_3"] == undefined ? 0 : response["TG_Dime_Rate_3"];

                        } else {

                            item["M_LetB_1"] = "";
                            item["MB_LetB_Rate_1"] = 0;
                            item["TG_LetB_Rate_1"] = 0;
                            item["MB_Dime_1"] = "";
                            item["TG_Dime_1"] = "";
                            item["MB_Dime_Rate_1"] = 0;
                            item["TG_Dime_Rate_1"] = 0;
                            item["M_LetB_2"] = "";
                            item["MB_LetB_Rate_2"] = 0;
                            item["TG_LetB_Rate_2"] = 0;
                            item["MB_Dime_2"] = "";
                            item["TG_Dime_2"] = "";
                            item["MB_Dime_Rate_2"] = 0;
                            item["TG_Dime_Rate_2"] = 0;
                            item["M_LetB_3"] = "";
                            item["MB_LetB_Rate_3"] = 0;
                            item["TG_LetB_Rate_3"] = 0;
                            item["MB_Dime_3"] = "";
                            item["TG_Dime_3"] = "";
                            item["MB_Dime_Rate_3"] = 0;
                            item["TG_Dime_Rate_3"] = 0;

                        }
                    }


                    if (item["CORNER"] === 1) {

                        let response = await getFT_CORNER_TODAY(thirdPartyAuthData, {ecid: item["ECID"]});

                        if (response != undefined && response != null) {

                            await dispatchFT_CORNER_TODAY(response);

                            item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];

                            item["M_LetB_CN"] = response["M_LetB"] == undefined ? "" : response["M_LetB"];
                            item["MB_LetB_Rate_CN"] = response["MB_LetB_Rate"] == undefined ? 0 : response["MB_LetB_Rate"];
                            item["TG_LetB_Rate_CN"] = response["TG_LetB_Rate"] == undefined ? 0 : response["TG_LetB_Rate"];

                            item["MB_Dime_CN"] = response["MB_Dime"] == undefined ? "" : response["MB_Dime"];
                            item["TG_Dime_CN"] = response["TG_Dime"] == undefined ? "" : response["TG_Dime"];
                            item["MB_Dime_Rate_CN"] = response["MB_Dime_Rate"] == undefined ? 0 : response["MB_Dime_Rate"];
                            item["TG_Dime_Rate_CN"] = response["TG_Dime_Rate"] == undefined ? 0 : response["TG_Dime_Rate"];

                            item["M_Flat_Rate_CN"] = response["M_Flat_Rate"] == undefined ? "" : response["M_Flat_Rate"];
                            item["MB_Win_Rate_CN"] = response["MB_Win_Rate"] == undefined ? 0 : response["MB_Win_Rate"];
                            item["TG_Win_Rate_CN"] = response["TG_Win_Rate"] == undefined ? 0 : response["TG_Win_Rate"];

                        } else {                        

                            item["CN_MID"] = "";

                            item["M_LetB_CN"] = "";
                            item["MB_LetB_Rate_CN"] = 0;
                            item["TG_LetB_Rate_CN"] = 0;

                            item["MB_Dime_CN"] = "";
                            item["TG_Dime_CN"] = "";
                            item["MB_Dime_Rate_CN"] = 0;
                            item["TG_Dime_Rate_CN"] = 0;

                            item["M_Flat_Rate_CN"] = 0;
                            item["MB_Win_Rate_CN"] = 0;
                            item["TG_Win_Rate_CN"] = 0;
                        }
                    }

                    if (item["LID"] !== "" && item["ECID"] !== "") {


                        let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});

                        if (moreData !== undefined && moreData != null) {
                                
                            item["MBMB"] = moreData["MBMB"] == undefined ? 0 : moreData["MBMB"];
                            item["MBFT"] = moreData["MBFT"] == undefined ? 0 : moreData["MBFT"];
                            item["MBTG"] = moreData["MBTG"] == undefined ? 0 : moreData["MBTG"];
                            item["FTMB"] = moreData["FTMB"] == undefined ? 0 : moreData["FTMB"];
                            item["FTFT"] = moreData["FTFT"] == undefined ? 0 : moreData["FTFT"];
                            item["FTTG"] = moreData["FTTG"] == undefined ? 0 : moreData["FTTG"];
                            item["TGMB"] = moreData["TGMB"] == undefined ? 0 : moreData["TGMB"];
                            item["TGTG"] = moreData["TGTG"] == undefined ? 0 : moreData["TGTG"];
                            item["TGFT"] = moreData["TGFT"] == undefined ? 0 : moreData["TGFT"];
                            item["F_Show"] = moreData["F_Show"] == undefined ? 0 : moreData["F_Show"];

                            item["S_0_1"] = moreData["S_0_1"] == undefined ? 0 : moreData["S_0_1"];
                            item["S_2_3"] = moreData["S_2_3"] == undefined ? 0 : moreData["S_2_3"];
                            item["S_4_6"] = moreData["S_4_6"] == undefined ? 0 : moreData["S_4_6"];
                            item["S_7UP"] = moreData["S_7UP"] == undefined ? 0 : moreData["S_7UP"];
                            item["T_Show"] = moreData["T_Show"] == undefined ? 0 : moreData["T_Show"];

                        } else {
                            item["MBMB"] = 0 ;
                            item["MBFT"] = 0;
                            item["MBTG"] = 0;
                            item["FTMB"] = 0;
                            item["FTFT"] = 0;
                            item["FTTG"] = 0;
                            item["TGMB"] = 0;
                            item["TGTG"] = 0;
                            item["TGFT"] = 0;
                            item["F_Show"] = 0;

                            item["S_0_1"] = 0;
                            item["S_2_3"] = 0;
                            item["S_4_6"] = 0;
                            item["S_7UP"] = 0;
                            item["T_Show"] = 0;
                        }
                    }

                }));
                await dispatchFT_DEFAULT_TODAY(itemList);
            }
            io.emit("receivedFTTodayMessage", itemList);
        }, 300000);
    })

    socket.on("stopFTTodayMessage", () => {
        clearInterval(leagueTodayInterval);
        clearInterval(ftTodayInterval);
        clearInterval(obtIterval);
    })

    // ======================= FT Early Data ========================== //

    socket.on("sendFTEarlyMessage", async (data) => {
        let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, data);
        // console.log(itemList)
        if (itemList && itemList.length > 0) {
            await Promise.all(itemList.map(async item => {
                if (item["HDP_OU"] === 1) {

                    let response = await getFT_HDP_OU_TODAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "early"});

                    if (response != undefined && response != null) {

                        item["M_LetB_1"] = response["M_LetB_1"] == undefined ? "" : response["M_LetB_1"];
                        item["MB_LetB_Rate_1"] = response["MB_LetB_Rate_1"] == undefined ? 0 : response["MB_LetB_Rate_1"];
                        item["TG_LetB_Rate_1"] = response["TG_LetB_Rate_1"] == undefined ? 0 : response["TG_LetB_Rate_1"];
                        item["MB_Dime_1"] = response["MB_Dime_1"] == undefined ? "" : response["MB_Dime_1"];
                        item["TG_Dime_1"] = response["TG_Dime_1"] == undefined ? "" : response["TG_Dime_1"];
                        item["MB_Dime_Rate_1"] = response["MB_Dime_Rate_1"] == undefined ? 0 : response["MB_Dime_Rate_1"];
                        item["TG_Dime_Rate_1"] = response["TG_Dime_Rate_1"] == undefined ? 0 : response["TG_Dime_Rate_1"];
                        item["M_LetB_2"] = response["M_LetB_2"] == undefined ? "" : response["M_LetB_2"];
                        item["MB_LetB_Rate_2"] = response["MB_LetB_Rate_2"] == undefined ? 0 : response["MB_LetB_Rate_2"];
                        item["TG_LetB_Rate_2"] = response["TG_LetB_Rate_2"] == undefined ? 0 : response["TG_LetB_Rate_2"];
                        item["MB_Dime_2"] = response["MB_Dime_2"] == undefined ? "" : response["MB_Dime_2"];
                        item["TG_Dime_2"] = response["TG_Dime_2"] == undefined ? "" : response["TG_Dime_2"];
                        item["MB_Dime_Rate_2"] = response["MB_Dime_Rate_2"] == undefined ? 0 : response["MB_Dime_Rate_2"];
                        item["TG_Dime_Rate_2"] = response["TG_Dime_Rate_2"] == undefined ? 0 : response["TG_Dime_Rate_2"];
                        item["M_LetB_3"] = response["M_LetB_3"] == undefined ? "" : response["M_LetB_3"];
                        item["MB_LetB_Rate_3"] = response["MB_LetB_Rate_3"] == undefined ? 0 : response["MB_LetB_Rate_3"];
                        item["TG_LetB_Rate_3"] = response["TG_LetB_Rate_3"] == undefined ? 0 : response["TG_LetB_Rate_3"];
                        item["MB_Dime_3"] = response["MB_Dime_3"] == undefined ? "" : response["MB_Dime_3"];
                        item["TG_Dime_3"] = response["TG_Dime_3"] == undefined ? "" : response["TG_Dime_3"];
                        item["MB_Dime_Rate_3"] = response["MB_Dime_Rate_3"] == undefined ? 0 : response["MB_Dime_Rate_3"];
                        item["TG_Dime_Rate_3"] = response["TG_Dime_Rate_3"] == undefined ? 0 : response["TG_Dime_Rate_3"];

                    } else {

                        item["M_LetB_1"] = "";
                        item["MB_LetB_Rate_1"] = 0;
                        item["TG_LetB_Rate_1"] = 0;
                        item["MB_Dime_1"] = "";
                        item["TG_Dime_1"] = "";
                        item["MB_Dime_Rate_1"] = 0;
                        item["TG_Dime_Rate_1"] = 0;
                        item["M_LetB_2"] = "";
                        item["MB_LetB_Rate_2"] = 0;
                        item["TG_LetB_Rate_2"] = 0;
                        item["MB_Dime_2"] = "";
                        item["TG_Dime_2"] = "";
                        item["MB_Dime_Rate_2"] = 0;
                        item["TG_Dime_Rate_2"] = 0;
                        item["M_LetB_3"] = "";
                        item["MB_LetB_Rate_3"] = 0;
                        item["TG_LetB_Rate_3"] = 0;
                        item["MB_Dime_3"] = "";
                        item["TG_Dime_3"] = "";
                        item["MB_Dime_Rate_3"] = 0;
                        item["TG_Dime_Rate_3"] = 0;

                    }
                }

            }));
            dispatchFT_DEFAULT_TODAY(itemList)            
        }
        io.emit("receivedFTEarlyMessage", itemList);
        ftEarlyInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, data);
            if (itemList && itemList.length > 0) {
                await Promise.all(itemList.map(async item => {
                    if (item["HDP_OU"] === 1) {

                        let response = await getFT_HDP_OU_TODAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "early"});

                        console.log(response);

                        if (response != undefined && response != null) {

                            item["M_LetB_1"] = response["M_LetB_1"] == undefined ? "" : response["M_LetB_1"];
                            item["MB_LetB_Rate_1"] = response["MB_LetB_Rate_1"] == undefined ? 0 : response["MB_LetB_Rate_1"];
                            item["TG_LetB_Rate_1"] = response["TG_LetB_Rate_1"] == undefined ? 0 : response["TG_LetB_Rate_1"];
                            item["MB_Dime_1"] = response["MB_Dime_1"] == undefined ? "" : response["MB_Dime_1"];
                            item["TG_Dime_1"] = response["TG_Dime_1"] == undefined ? "" : response["TG_Dime_1"];
                            item["MB_Dime_Rate_1"] = response["MB_Dime_Rate_1"] == undefined ? 0 : response["MB_Dime_Rate_1"];
                            item["TG_Dime_Rate_1"] = response["TG_Dime_Rate_1"] == undefined ? 0 : response["TG_Dime_Rate_1"];
                            item["M_LetB_2"] = response["M_LetB_2"] == undefined ? "" : response["M_LetB_2"];
                            item["MB_LetB_Rate_2"] = response["MB_LetB_Rate_2"] == undefined ? 0 : response["MB_LetB_Rate_2"];
                            item["TG_LetB_Rate_2"] = response["TG_LetB_Rate_2"] == undefined ? 0 : response["TG_LetB_Rate_2"];
                            item["MB_Dime_2"] = response["MB_Dime_2"] == undefined ? "" : response["MB_Dime_2"];
                            item["TG_Dime_2"] = response["TG_Dime_2"] == undefined ? "" : response["TG_Dime_2"];
                            item["MB_Dime_Rate_2"] = response["MB_Dime_Rate_2"] == undefined ? 0 : response["MB_Dime_Rate_2"];
                            item["TG_Dime_Rate_2"] = response["TG_Dime_Rate_2"] == undefined ? 0 : response["TG_Dime_Rate_2"];
                            item["M_LetB_3"] = response["M_LetB_3"] == undefined ? "" : response["M_LetB_3"];
                            item["MB_LetB_Rate_3"] = response["MB_LetB_Rate_3"] == undefined ? 0 : response["MB_LetB_Rate_3"];
                            item["TG_LetB_Rate_3"] = response["TG_LetB_Rate_3"] == undefined ? 0 : response["TG_LetB_Rate_3"];
                            item["MB_Dime_3"] = response["MB_Dime_3"] == undefined ? "" : response["MB_Dime_3"];
                            item["TG_Dime_3"] = response["TG_Dime_3"] == undefined ? "" : response["TG_Dime_3"];
                            item["MB_Dime_Rate_3"] = response["MB_Dime_Rate_3"] == undefined ? 0 : response["MB_Dime_Rate_3"];
                            item["TG_Dime_Rate_3"] = response["TG_Dime_Rate_3"] == undefined ? 0 : response["TG_Dime_Rate_3"];

                        } else {

                            item["M_LetB_1"] = "";
                            item["MB_LetB_Rate_1"] = 0;
                            item["TG_LetB_Rate_1"] = 0;
                            item["MB_Dime_1"] = "";
                            item["TG_Dime_1"] = "";
                            item["MB_Dime_Rate_1"] = 0;
                            item["TG_Dime_Rate_1"] = 0;
                            item["M_LetB_2"] = "";
                            item["MB_LetB_Rate_2"] = 0;
                            item["TG_LetB_Rate_2"] = 0;
                            item["MB_Dime_2"] = "";
                            item["TG_Dime_2"] = "";
                            item["MB_Dime_Rate_2"] = 0;
                            item["TG_Dime_Rate_2"] = 0;
                            item["M_LetB_3"] = "";
                            item["MB_LetB_Rate_3"] = 0;
                            item["TG_LetB_Rate_3"] = 0;
                            item["MB_Dime_3"] = "";
                            item["TG_Dime_3"] = "";
                            item["MB_Dime_Rate_3"] = 0;
                            item["TG_Dime_Rate_3"] = 0;

                        }
                    }

                }));
                dispatchFT_DEFAULT_TODAY(itemList)            
            }            
            io.emit("receivedFTEarlyMessage", itemList);
        }, 300000);
    })

    socket.on("stopFTEarlyMessage", () => {
        clearInterval(leagueEarlyInterval);
        clearInterval(ftEarlyInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Champion Data ======================//

    socket.on("sendChampionMainMessage", async (data) => {
        let itemList = await getFT_MAIN_CHAMPION(thirdPartyAuthData, data);
        // console.log(itemList)
        io.emit("receivedFTChampionMessage", itemList);
        if (Array.isArray(itemList) && itemList.length > 0) {
            dispatchFT_MAIN_CHAMPION(itemList) 
        } else if (itemList) {
            let tempItemList = [];
            tempItemList.push(itemList);
            dispatchFT_MAIN_CHAMPION(tempItemList)    
        }
        ftChampionInterval = setInterval(async () => {
            let itemList = await getFT_MAIN_CHAMPION(thirdPartyAuthData, data);
            io.emit("receivedFTChampionMessage", itemList);
            if (Array.isArray(itemList) && itemList.length > 0) {
                dispatchFT_MAIN_CHAMPION(itemList) 
            } else if (itemList) {
                let tempItemList = [];
                tempItemList.push(itemList);
                dispatchFT_MAIN_CHAMPION(tempItemList)    
            }
        }, 300000);
    })

    socket.on("stopFTChampionMessage", () => {
        clearInterval(ftChampionInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Parlay Data ======================//

    socket.on("sendFTParlayMessage", async (data) => {
        let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, data);
        // console.log("111111111111111111", itemList)
        if (itemList && itemList.length > 0) {
            await Promise.all(itemList.map(async item => {
                if (item["HDP_OU"] === 1) {

                    let response = await getFT_HDP_OU_PARLAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], field: data["field"], showtype: "parlay"});

                    // console.log(response);

                    if (response != undefined && response != null) {

                        item["M_P_LetB_1"] = response["M_P_LetB_RB_1"] == undefined ? "" : response["M_P_LetB_RB_1"];
                        item["MB_P_LetB_Rate_1"] = response["MB_P_LetB_Rate_RB_1"] == undefined ? 0 : response["MB_P_LetB_Rate_RB_1"];
                        item["TG_P_LetB_Rate_1"] = response["TG_P_LetB_Rate_RB_1"] == undefined ? 0 : response["TG_P_LetB_Rate_RB_1"];
                        item["MB_P_Dime_1"] = response["MB_P_Dime_RB_1"] == undefined ? "" : response["MB_P_Dime_RB_1"];
                        item["TG_P_Dime_1"] = response["TG_P_Dime_RB_1"] == undefined ? "" : response["TG_P_Dime_RB_1"];
                        item["MB_P_Dime_Rate_1"] = response["MB_P_Dime_Rate_RB_1"] == undefined ? 0 : response["MB_P_Dime_Rate_RB_1"];
                        item["TG_P_Dime_Rate_1"] = response["TG_P_Dime_Rate_RB_1"] == undefined ? 0 : response["TG_P_Dime_Rate_RB_1"];
                        item["M_P_LetB_2"] = response["M_P_LetB_RB_2"] == undefined ? "" : response["M_P_LetB_RB_2"];
                        item["MB_P_LetB_Rate_2"] = response["MB_P_LetB_Rate_RB_2"] == undefined ? 0 : response["MB_P_LetB_Rate_RB_2"];
                        item["TG_P_LetB_Rate_2"] = response["TG_P_LetB_Rate_RB_2"] == undefined ? 0 : response["TG_P_LetB_Rate_RB_2"];
                        item["MB_P_Dime_2"] = response["MB_P_Dime_RB_2"] == undefined ? "" : response["MB_P_Dime_RB_2"];
                        item["TG_P_Dime_2"] = response["TG_P_Dime_RB_2"] == undefined ? "" : response["TG_P_Dime_RB_2"];
                        item["MB_P_Dime_Rate_2"] = response["MB_P_Dime_Rate_RB_2"] == undefined ? 0 : response["MB_P_Dime_Rate_RB_2"];
                        item["TG_P_Dime_Rate_2"] = response["TG_P_Dime_Rate_RB_2"] == undefined ? 0 : response["TG_P_Dime_Rate_RB_2"];
                        item["M_P_LetB_3"] = response["M_P_LetB_RB_3"] == undefined ? "" : response["M_P_LetB_RB_3"];
                        item["MB_P_LetB_Rate_3"] = response["MB_P_LetB_Rate_RB_3"] == undefined ? 0 : response["MB_P_LetB_Rate_RB_3"];
                        item["TG_P_LetB_Rate_3"] = response["TG_P_LetB_Rate_RB_3"] == undefined ? 0 : response["TG_P_LetB_Rate_RB_3"];
                        item["MB_P_Dime_3"] = response["MB_P_Dime_RB_3"] == undefined ? "" : response["MB_P_Dime_RB_3"];
                        item["TG_P_Dime_3"] = response["TG_P_Dime_RB_3"] == undefined ? "" : response["TG_P_Dime_RB_3"];
                        item["MB_P_Dime_Rate_3"] = response["MB_P_Dime_Rate_RB_3"] == undefined ? 0 : response["MB_P_Dime_Rate_RB_3"];
                        item["TG_P_Dime_Rate_3"] = response["TG_P_Dime_Rate_RB_3"] == undefined ? 0 : response["TG_P_Dime_Rate_RB_3"];

                    } else {

                        item["M_P_LetB_1"] = "";
                        item["MB_P_LetB_Rate_1"] = 0;
                        item["TG_P_LetB_Rate_1"] = 0;
                        item["MB_P_Dime_1"] = "";
                        item["TG_P_Dime_1"] = "";
                        item["MB_P_Dime_Rate_1"] = 0;
                        item["TG_P_Dime_Rate_1"] = 0;
                        item["M_P_LetB_2"] = "";
                        item["MB_P_LetB_Rate_2"] = 0;
                        item["TG_P_LetB_Rate_2"] = 0;
                        item["MB_P_Dime_2"] = "";
                        item["TG_P_Dime_2"] = "";
                        item["MB_P_Dime_Rate_2"] = 0;
                        item["TG_P_Dime_Rate_2"] = 0;
                        item["M_P_LetB_3"] = "";
                        item["MB_P_LetB_Rate_3"] = 0;
                        item["TG_P_LetB_Rate_3"] = 0;
                        item["MB_P_Dime_3"] = "";
                        item["TG_P_Dime_3"] = "";
                        item["MB_P_Dime_Rate_3"] = 0;
                        item["TG_P_Dime_Rate_3"] = 0;

                    }
                }

            }));
            dispatchFT_DEFAULT_PARLAY(itemList)            
        }

        io.emit("receivedFTParlayMessage", itemList);

        ftParlayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, data);
            if (itemList && itemList.length > 0) {
                await Promise.all(itemList.map(async item => {
                    if (item["HDP_OU"] === 1) {

                        let response = await getFT_HDP_OU_PARLAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "parlay", field: data["field"]});

                        if (response != undefined && response != null) {

                            item["M_P_LetB_1"] = response["M_LetB_RB_1"] == undefined ? "" : response["M_LetB_RB_1"];
                            item["MB_P_LetB_Rate_1"] = response["MB_LetB_Rate_RB_1"] == undefined ? 0 : response["MB_LetB_Rate_RB_1"];
                            item["TG_P_LetB_Rate_1"] = response["TG_LetB_Rate_RB_1"] == undefined ? 0 : response["TG_LetB_Rate_RB_1"];
                            item["MB_P_Dime_1"] = response["MB_Dime_RB_1"] == undefined ? "" : response["MB_Dime_RB_1"];
                            item["TG_P_Dime_1"] = response["TG_Dime_RB_1"] == undefined ? "" : response["TG_Dime_RB_1"];
                            item["MB_P_Dime_Rate_1"] = response["MB_Dime_Rate_RB_1"] == undefined ? 0 : response["MB_Dime_Rate_RB_1"];
                            item["TG_P_Dime_Rate_1"] = response["TG_Dime_Rate_RB_1"] == undefined ? 0 : response["TG_Dime_Rate_RB_1"];
                            item["M_P_LetB_2"] = response["M_LetB_RB_2"] == undefined ? "" : response["M_LetB_RB_2"];
                            item["MB_P_LetB_Rate_2"] = response["MB_LetB_Rate_RB_2"] == undefined ? 0 : response["MB_LetB_Rate_RB_2"];
                            item["TG_P_LetB_Rate_2"] = response["TG_LetB_Rate_RB_2"] == undefined ? 0 : response["TG_LetB_Rate_RB_2"];
                            item["MB_P_Dime_2"] = response["MB_Dime_RB_2"] == undefined ? "" : response["MB_Dime_RB_2"];
                            item["TG_P_Dime_2"] = response["TG_Dime_RB_2"] == undefined ? "" : response["TG_Dime_RB_2"];
                            item["MB_P_Dime_Rate_2"] = response["MB_Dime_Rate_RB_2"] == undefined ? 0 : response["MB_Dime_Rate_RB_2"];
                            item["TG_P_Dime_Rate_2"] = response["TG_Dime_Rate_RB_2"] == undefined ? 0 : response["TG_Dime_Rate_RB_2"];
                            item["M_P_LetB_3"] = response["M_LetB_RB_3"] == undefined ? "" : response["M_LetB_RB_3"];
                            item["MB_P_LetB_Rate_3"] = response["MB_LetB_Rate_RB_3"] == undefined ? 0 : response["MB_LetB_Rate_RB_3"];
                            item["TG_P_LetB_Rate_3"] = response["TG_LetB_Rate_RB_3"] == undefined ? 0 : response["TG_LetB_Rate_RB_3"];
                            item["MB_P_Dime_3"] = response["MB_Dime_RB_3"] == undefined ? "" : response["MB_Dime_RB_3"];
                            item["TG_P_Dime_3"] = response["TG_Dime_RB_3"] == undefined ? "" : response["TG_Dime_RB_3"];
                            item["MB_P_Dime_Rate_3"] = response["MB_Dime_Rate_RB_3"] == undefined ? 0 : response["MB_Dime_Rate_RB_3"];
                            item["TG_P_Dime_Rate_3"] = response["TG_Dime_Rate_RB_3"] == undefined ? 0 : response["TG_Dime_Rate_RB_3"];

                        } else {

                            item["M_P_LetB_1"] = "";
                            item["MB_P_LetB_Rate_1"] = 0;
                            item["TG_P_LetB_Rate_1"] = 0;
                            item["MB_P_Dime_1"] = "";
                            item["TG_P_Dime_1"] = "";
                            item["MB_P_Dime_Rate_1"] = 0;
                            item["TG_P_Dime_Rate_1"] = 0;
                            item["M_P_LetB_2"] = "";
                            item["MB_P_LetB_Rate_2"] = 0;
                            item["TG_P_LetB_Rate_2"] = 0;
                            item["MB_P_Dime_2"] = "";
                            item["TG_P_Dime_2"] = "";
                            item["MB_P_Dime_Rate_2"] = 0;
                            item["TG_P_Dime_Rate_2"] = 0;
                            item["M_P_LetB_3"] = "";
                            item["MB_P_LetB_Rate_3"] = 0;
                            item["TG_P_LetB_Rate_3"] = 0;
                            item["MB_P_Dime_3"] = "";
                            item["TG_P_Dime_3"] = "";
                            item["MB_P_Dime_Rate_3"] = 0;
                            item["TG_P_Dime_Rate_3"] = 0;

                        }
                    }

                }));
                dispatchFT_DEFAULT_PARLAY(itemList)            
            }            
            io.emit("receivedFTParlayMessage", itemList);
        }, 300000);
    })

    socket.on("stopFTParlayMessage", () => {
        clearInterval(leagueParlayInterval);
        clearInterval(ftParlayInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Favorite Data ======================//

    socket.on("sendFTFavoriteMessage", async (data) => {
        // console.log(data);
        clearInterval(ftFavoriteInterval);
        let itemList = await getFT_MAIN_FAVORITE(thirdPartyAuthData, data);
        // console.log(itemList)
        if (itemList && itemList.length > 0) {

            await Promise.all(itemList.map(async item => {

                if (item["HDP_OU"] === 1) {
                    if (item["showType"] == "rb") {
                        let response = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"]});
                        if (response != undefined && response != null) {

                            item["M_LetB_RB_1"] = response["M_LetB_RB_1"] == undefined ? "" : response["M_LetB_RB_1"];
                            item["MB_LetB_Rate_RB_1"] = response["MB_LetB_Rate_RB_1"] == undefined ? 0 : response["MB_LetB_Rate_RB_1"];
                            item["TG_LetB_Rate_RB_1"] = response["TG_LetB_Rate_RB_1"] == undefined ? 0 : response["TG_LetB_Rate_RB_1"];
                            item["MB_Dime_RB_1"] = response["MB_Dime_RB_1"] == undefined ? "" : response["MB_Dime_RB_1"];
                            item["TG_Dime_RB_1"] = response["TG_Dime_RB_1"] == undefined ? "" : response["TG_Dime_RB_1"];
                            item["MB_Dime_Rate_RB_1"] = response["MB_Dime_Rate_RB_1"] == undefined ? 0 : response["MB_Dime_Rate_RB_1"];
                            item["TG_Dime_Rate_RB_1"] = response["TG_Dime_Rate_RB_1"] == undefined ? 0 : response["TG_Dime_Rate_RB_1"];
                            item["M_LetB_RB_2"] = response["M_LetB_RB_2"] == undefined ? "" : response["M_LetB_RB_2"];
                            item["MB_LetB_Rate_RB_2"] = response["MB_LetB_Rate_RB_2"] == undefined ? 0 : response["MB_LetB_Rate_RB_2"];
                            item["TG_LetB_Rate_RB_2"] = response["TG_LetB_Rate_RB_2"] == undefined ? 0 : response["TG_LetB_Rate_RB_2"];
                            item["MB_Dime_RB_2"] = response["MB_Dime_RB_2"] == undefined ? "" : response["MB_Dime_RB_2"];
                            item["TG_Dime_RB_2"] = response["TG_Dime_RB_2"] == undefined ? "" : response["TG_Dime_RB_2"];
                            item["MB_Dime_Rate_RB_2"] = response["MB_Dime_Rate_RB_2"] == undefined ? 0 : response["MB_Dime_Rate_RB_2"];
                            item["TG_Dime_Rate_RB_2"] = response["TG_Dime_Rate_RB_2"] == undefined ? 0 : response["TG_Dime_Rate_RB_2"];
                            item["M_LetB_RB_3"] = response["M_LetB_RB_3"] == undefined ? "" : response["M_LetB_RB_3"];
                            item["MB_LetB_Rate_RB_3"] = response["MB_LetB_Rate_RB_3"] == undefined ? 0 : response["MB_LetB_Rate_RB_3"];
                            item["TG_LetB_Rate_RB_3"] = response["TG_LetB_Rate_RB_3"] == undefined ? 0 : response["TG_LetB_Rate_RB_3"];
                            item["MB_Dime_RB_3"] = response["MB_Dime_RB_3"] == undefined ? "" : response["MB_Dime_RB_3"];
                            item["TG_Dime_RB_3"] = response["TG_Dime_RB_3"] == undefined ? "" : response["TG_Dime_RB_3"];
                            item["MB_Dime_Rate_RB_3"] = response["MB_Dime_Rate_RB_3"] == undefined ? 0 : response["MB_Dime_Rate_RB_3"];
                            item["TG_Dime_Rate_RB_3"] = response["TG_Dime_Rate_RB_3"] == undefined ? 0 : response["TG_Dime_Rate_RB_3"];

                        } else {

                            item["M_LetB_RB_1"] = "";
                            item["MB_LetB_Rate_RB_1"] = 0;
                            item["TG_LetB_Rate_RB_1"] = 0;
                            item["MB_Dime_RB_1"] = "";
                            item["TG_Dime_RB_1"] = "";
                            item["MB_Dime_Rate_RB_1"] = 0;
                            item["TG_Dime_Rate_RB_1"] = 0;
                            item["M_LetB_RB_2"] = "";
                            item["MB_LetB_Rate_RB_2"] = 0;
                            item["TG_LetB_Rate_RB_2"] = 0;
                            item["MB_Dime_RB_2"] = "";
                            item["TG_Dime_RB_2"] = "";
                            item["MB_Dime_Rate_RB_2"] = 0;
                            item["TG_Dime_Rate_RB_2"] = 0;
                            item["M_LetB_RB_3"] = "";
                            item["MB_LetB_Rate_RB_3"] = 0;
                            item["TG_LetB_Rate_RB_3"] = 0;
                            item["MB_Dime_RB_3"] = "";
                            item["TG_Dime_RB_3"] = "";
                            item["MB_Dime_Rate_RB_3"] = 0;
                            item["TG_Dime_Rate_RB_3"] = 0;

                        }
                    } else {
                        let response = await getFT_HDP_OU_TODAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "parlay", field: data["field"]});

                        if (response != undefined && response != null) {

                            item["M_LetB_1"] = response["M_LetB_1"] == undefined ? "" : response["M_LetB_1"];
                            item["MB_LetB_Rate_1"] = response["MB_LetB_Rate_1"] == undefined ? 0 : response["MB_LetB_Rate_1"];
                            item["TG_LetB_Rate_1"] = response["TG_LetB_Rate_1"] == undefined ? 0 : response["TG_LetB_Rate_1"];
                            item["MB_Dime_1"] = response["MB_Dime_1"] == undefined ? "" : response["MB_Dime_1"];
                            item["TG_Dime_1"] = response["TG_Dime_1"] == undefined ? "" : response["TG_Dime_1"];
                            item["MB_Dime_Rate_1"] = response["MB_Dime_Rate_1"] == undefined ? 0 : response["MB_Dime_Rate_1"];
                            item["TG_Dime_Rate_1"] = response["TG_Dime_Rate_1"] == undefined ? 0 : response["TG_Dime_Rate_1"];
                            item["M_LetB_2"] = response["M_LetB_2"] == undefined ? "" : response["M_LetB_2"];
                            item["MB_LetB_Rate_2"] = response["MB_LetB_Rate_2"] == undefined ? 0 : response["MB_LetB_Rate_2"];
                            item["TG_LetB_Rate_2"] = response["TG_LetB_Rate_2"] == undefined ? 0 : response["TG_LetB_Rate_2"];
                            item["MB_Dime_2"] = response["MB_Dime_2"] == undefined ? "" : response["MB_Dime_2"];
                            item["TG_Dime_2"] = response["TG_Dime_2"] == undefined ? "" : response["TG_Dime_2"];
                            item["MB_Dime_Rate_2"] = response["MB_Dime_Rate_2"] == undefined ? 0 : response["MB_Dime_Rate_2"];
                            item["TG_Dime_Rate_2"] = response["TG_Dime_Rate_2"] == undefined ? 0 : response["TG_Dime_Rate_2"];
                            item["M_LetB_3"] = response["M_LetB_3"] == undefined ? "" : response["M_LetB_3"];
                            item["MB_LetB_Rate_3"] = response["MB_LetB_Rate_3"] == undefined ? 0 : response["MB_LetB_Rate_3"];
                            item["TG_LetB_Rate_3"] = response["TG_LetB_Rate_3"] == undefined ? 0 : response["TG_LetB_Rate_3"];
                            item["MB_Dime_3"] = response["MB_Dime_3"] == undefined ? "" : response["MB_Dime_3"];
                            item["TG_Dime_3"] = response["TG_Dime_3"] == undefined ? "" : response["TG_Dime_3"];
                            item["MB_Dime_Rate_3"] = response["MB_Dime_Rate_3"] == undefined ? 0 : response["MB_Dime_Rate_3"];
                            item["TG_Dime_Rate_3"] = response["TG_Dime_Rate_3"] == undefined ? 0 : response["TG_Dime_Rate_3"];

                        } else {

                            item["M_LetB_1"] = "";
                            item["MB_LetB_Rate_1"] = 0;
                            item["TG_LetB_Rate_1"] = 0;
                            item["MB_Dime_1"] = "";
                            item["TG_Dime_1"] = "";
                            item["MB_Dime_Rate_1"] = 0;
                            item["TG_Dime_Rate_1"] = 0;
                            item["M_LetB_2"] = "";
                            item["MB_LetB_Rate_2"] = 0;
                            item["TG_LetB_Rate_2"] = 0;
                            item["MB_Dime_2"] = "";
                            item["TG_Dime_2"] = "";
                            item["MB_Dime_Rate_2"] = 0;
                            item["TG_Dime_Rate_2"] = 0;
                            item["M_LetB_3"] = "";
                            item["MB_LetB_Rate_3"] = 0;
                            item["TG_LetB_Rate_3"] = 0;
                            item["MB_Dime_3"] = "";
                            item["TG_Dime_3"] = "";
                            item["MB_Dime_Rate_3"] = 0;
                            item["TG_Dime_Rate_3"] = 0;

                        }

                    }
                }


                if (item["CORNER"] === 1 && item["showType"] === "rb") {

                    let response = await getFT_CORNER_INPLAY(thirdPartyAuthData, {ecid: item["ECID"]});

                    if (response != undefined && response != null) {

                        await dispatchFT_CORNER_INPLAY(response);

                        item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];
                        item["MB_Dime_RB_CN"] = response["MB_Dime_RB"] == undefined ? "" : response["MB_Dime_RB"];
                        item["TG_Dime_RB_CN"] = response["TG_Dime_RB"] == undefined ? "" : response["TG_Dime_RB"];
                        item["MB_Dime_Rate_RB_CN"] = response["MB_Dime_Rate_RB"] == undefined ? 0 : response["MB_Dime_Rate_RB"];
                        item["TG_Dime_Rate_RB_CN"] = response["TG_Dime_Rate_RB"] == undefined ? 0 : response["TG_Dime_Rate_RB"];
                        item["MB_Dime_RB_H_CN"] = response["MB_Dime_RB_H"] == undefined ? "" : response["MB_Dime_RB_H"];
                        item["TG_Dime_RB_H_CN"] = response["TG_Dime_RB_H"] == undefined ? "" : response["TG_Dime_RB_H"];
                        item["MB_Dime_Rate_RB_H_CN"] = response["MB_Dime_Rate_RB_H"] == undefined ? 0 : response["MB_Dime_Rate_RB_H"];
                        item["TG_Dime_Rate_RB_H_CN"] = response["TG_Dime_Rate_RB_H"] == undefined ? 0 : response["TG_Dime_Rate_RB_H"];
                        item["S_Single_Rate_CN"] = response["S_Single_Rate"] == undefined ? 0 : response["S_Single_Rate"];
                        item["S_Double_Rate_CN"] = response["S_Double_Rate"] == undefined ? 0 : response["S_Double_Rate"];
                        item["S_Single_Rate_H_CN"] = response["S_Single_Rate_H"] == undefined ? 0 : response["S_Single_Rate_H"];
                        item["S_Double_Rate_H_CN"] = response["S_Double_Rate_H"] == undefined ? 0 : response["S_Double_Rate_H"];

                    } else {

                        item["CN_MID"] = response["MID"];
                        item["MB_Dime_RB_CN"] = "";
                        item["TG_Dime_RB_CN"] = "";
                        item["MB_Dime_Rate_RB_CN"] = 0;
                        item["TG_Dime_Rate_RB_CN"] = 0;
                        item["MB_Dime_RB_H_CN"] = "";
                        item["TG_Dime_RB_H_CN"] = "";
                        item["MB_Dime_Rate_RB_H_CN"] = 0;
                        item["TG_Dime_Rate_RB_H_CN"] = 0;
                        item["S_Single_Rate_CN"] = 0;
                        item["S_Double_Rate_CN"] = 0;
                        item["S_Single_Rate_H_CN"] = 0;
                        item["S_Double_Rate_H_CN"] = 0;

                    }

                } else if (item["CORNER"] === 1 && item["showType"] === "ft") {

                    let response = await getFT_CORNER_TODAY(thirdPartyAuthData, {ecid: item["ECID"]});

                    if (response != undefined && response != null) {

                        await dispatchFT_CORNER_TODAY(response);

                        item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];

                        item["M_LetB_CN"] = response["M_LetB"] == undefined ? "" : response["M_LetB"];
                        item["MB_LetB_Rate_CN"] = response["MB_LetB_Rate"] == undefined ? 0 : response["MB_LetB_Rate"];
                        item["TG_LetB_Rate_CN"] = response["TG_LetB_Rate"] == undefined ? 0 : response["TG_LetB_Rate"];

                        item["MB_Dime_CN"] = response["MB_Dime"] == undefined ? "" : response["MB_Dime"];
                        item["TG_Dime_CN"] = response["TG_Dime"] == undefined ? "" : response["TG_Dime"];
                        item["MB_Dime_Rate_CN"] = response["MB_Dime_Rate"] == undefined ? 0 : response["MB_Dime_Rate"];
                        item["TG_Dime_Rate_CN"] = response["TG_Dime_Rate"] == undefined ? 0 : response["TG_Dime_Rate"];

                        item["M_Flat_Rate_CN"] = response["M_Flat_Rate"] == undefined ? "" : response["M_Flat_Rate"];
                        item["MB_Win_Rate_CN"] = response["MB_Win_Rate"] == undefined ? 0 : response["MB_Win_Rate"];
                        item["TG_Win_Rate_CN"] = response["TG_Win_Rate"] == undefined ? 0 : response["TG_Win_Rate"];

                    } else {                        

                        item["CN_MID"] = "";

                        item["M_LetB_CN"] = "";
                        item["MB_LetB_Rate_CN"] = 0;
                        item["TG_LetB_Rate_CN"] = 0;

                        item["MB_Dime_CN"] = "";
                        item["TG_Dime_CN"] = "";
                        item["MB_Dime_Rate_CN"] = 0;
                        item["TG_Dime_Rate_CN"] = 0;

                        item["M_Flat_Rate_CN"] = 0;
                        item["MB_Win_Rate_CN"] = 0;
                        item["TG_Win_Rate_CN"] = 0;
                    }

                }

                if (item["LID"] !== "" && item["ECID"] !== "" && item["showType"] == "ft") {


                    let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});

                    // console.log(moreData);

                    if (moreData !== undefined && moreData != null && moreData != {}) {
                            
                        item["MBMB"] = moreData["MBMB"] == undefined ? 0 : moreData["MBMB"];
                        item["MBFT"] = moreData["MBFT"] == undefined ? 0 : moreData["MBFT"];
                        item["MBTG"] = moreData["MBTG"] == undefined ? 0 : moreData["MBTG"];
                        item["FTMB"] = moreData["FTMB"] == undefined ? 0 : moreData["FTMB"];
                        item["FTFT"] = moreData["FTFT"] == undefined ? 0 : moreData["FTFT"];
                        item["FTTG"] = moreData["FTTG"] == undefined ? 0 : moreData["FTTG"];
                        item["TGMB"] = moreData["TGMB"] == undefined ? 0 : moreData["TGMB"];
                        item["TGTG"] = moreData["TGTG"] == undefined ? 0 : moreData["TGTG"];
                        item["TGFT"] = moreData["TGFT"] == undefined ? 0 : moreData["TGFT"];
                        item["F_Show"] = moreData["F_Show"] == undefined ? 0 : moreData["F_Show"];

                        item["S_0_1"] = moreData["S_0_1"] == undefined ? 0 : moreData["S_0_1"];
                        item["S_2_3"] = moreData["S_2_3"] == undefined ? 0 : moreData["S_2_3"];
                        item["S_4_6"] = moreData["S_4_6"] == undefined ? 0 : moreData["S_4_6"];
                        item["S_7UP"] = moreData["S_7UP"] == undefined ? 0 : moreData["S_7UP"];
                        item["T_Show"] = moreData["T_Show"] == undefined ? 0 : moreData["T_Show"];

                    } else {
                        item["MBMB"] = 0 ;
                        item["MBFT"] = 0;
                        item["MBTG"] = 0;
                        item["FTMB"] = 0;
                        item["FTFT"] = 0;
                        item["FTTG"] = 0;
                        item["TGMB"] = 0;
                        item["TGTG"] = 0;
                        item["TGFT"] = 0;
                        item["F_Show"] = 0;

                        item["S_0_1"] = 0;
                        item["S_2_3"] = 0;
                        item["S_4_6"] = 0;
                        item["S_7UP"] = 0;
                        item["T_Show"] = 0;
                    }
                    
                }
            }));

            dispatchFT_MAIN_FAVORITE(itemList)            
        }

        io.emit("receivedFTFavoriteMessage", itemList);

        ftFavoriteInterval = setInterval(async () => {

            let itemList = await getFT_MAIN_FAVORITE(thirdPartyAuthData, data);

            if (itemList && itemList.length > 0) {

                await Promise.all(itemList.map(async item => {

                    if (item["HDP_OU"] === 1) {
                        if (item["showType"] == "rb") {
                            let response = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"]});
                            if (response != undefined && response != null) {

                                item["M_LetB_RB_1"] = response["M_LetB_RB_1"] == undefined ? "" : response["M_LetB_RB_1"];
                                item["MB_LetB_Rate_RB_1"] = response["MB_LetB_Rate_RB_1"] == undefined ? 0 : response["MB_LetB_Rate_RB_1"];
                                item["TG_LetB_Rate_RB_1"] = response["TG_LetB_Rate_RB_1"] == undefined ? 0 : response["TG_LetB_Rate_RB_1"];
                                item["MB_Dime_RB_1"] = response["MB_Dime_RB_1"] == undefined ? "" : response["MB_Dime_RB_1"];
                                item["TG_Dime_RB_1"] = response["TG_Dime_RB_1"] == undefined ? "" : response["TG_Dime_RB_1"];
                                item["MB_Dime_Rate_RB_1"] = response["MB_Dime_Rate_RB_1"] == undefined ? 0 : response["MB_Dime_Rate_RB_1"];
                                item["TG_Dime_Rate_RB_1"] = response["TG_Dime_Rate_RB_1"] == undefined ? 0 : response["TG_Dime_Rate_RB_1"];
                                item["M_LetB_RB_2"] = response["M_LetB_RB_2"] == undefined ? "" : response["M_LetB_RB_2"];
                                item["MB_LetB_Rate_RB_2"] = response["MB_LetB_Rate_RB_2"] == undefined ? 0 : response["MB_LetB_Rate_RB_2"];
                                item["TG_LetB_Rate_RB_2"] = response["TG_LetB_Rate_RB_2"] == undefined ? 0 : response["TG_LetB_Rate_RB_2"];
                                item["MB_Dime_RB_2"] = response["MB_Dime_RB_2"] == undefined ? "" : response["MB_Dime_RB_2"];
                                item["TG_Dime_RB_2"] = response["TG_Dime_RB_2"] == undefined ? "" : response["TG_Dime_RB_2"];
                                item["MB_Dime_Rate_RB_2"] = response["MB_Dime_Rate_RB_2"] == undefined ? 0 : response["MB_Dime_Rate_RB_2"];
                                item["TG_Dime_Rate_RB_2"] = response["TG_Dime_Rate_RB_2"] == undefined ? 0 : response["TG_Dime_Rate_RB_2"];
                                item["M_LetB_RB_3"] = response["M_LetB_RB_3"] == undefined ? "" : response["M_LetB_RB_3"];
                                item["MB_LetB_Rate_RB_3"] = response["MB_LetB_Rate_RB_3"] == undefined ? 0 : response["MB_LetB_Rate_RB_3"];
                                item["TG_LetB_Rate_RB_3"] = response["TG_LetB_Rate_RB_3"] == undefined ? 0 : response["TG_LetB_Rate_RB_3"];
                                item["MB_Dime_RB_3"] = response["MB_Dime_RB_3"] == undefined ? "" : response["MB_Dime_RB_3"];
                                item["TG_Dime_RB_3"] = response["TG_Dime_RB_3"] == undefined ? "" : response["TG_Dime_RB_3"];
                                item["MB_Dime_Rate_RB_3"] = response["MB_Dime_Rate_RB_3"] == undefined ? 0 : response["MB_Dime_Rate_RB_3"];
                                item["TG_Dime_Rate_RB_3"] = response["TG_Dime_Rate_RB_3"] == undefined ? 0 : response["TG_Dime_Rate_RB_3"];

                            } else {

                                item["M_LetB_RB_1"] = "";
                                item["MB_LetB_Rate_RB_1"] = 0;
                                item["TG_LetB_Rate_RB_1"] = 0;
                                item["MB_Dime_RB_1"] = "";
                                item["TG_Dime_RB_1"] = "";
                                item["MB_Dime_Rate_RB_1"] = 0;
                                item["TG_Dime_Rate_RB_1"] = 0;
                                item["M_LetB_RB_2"] = "";
                                item["MB_LetB_Rate_RB_2"] = 0;
                                item["TG_LetB_Rate_RB_2"] = 0;
                                item["MB_Dime_RB_2"] = "";
                                item["TG_Dime_RB_2"] = "";
                                item["MB_Dime_Rate_RB_2"] = 0;
                                item["TG_Dime_Rate_RB_2"] = 0;
                                item["M_LetB_RB_3"] = "";
                                item["MB_LetB_Rate_RB_3"] = 0;
                                item["TG_LetB_Rate_RB_3"] = 0;
                                item["MB_Dime_RB_3"] = "";
                                item["TG_Dime_RB_3"] = "";
                                item["MB_Dime_Rate_RB_3"] = 0;
                                item["TG_Dime_Rate_RB_3"] = 0;

                            }
                        } else {
                            let response = await getFT_HDP_OU_TODAY(thirdPartyAuthData, {id: item["MID"], ecid: item["ECID"], showtype: "parlay", field: data["field"]});

                            if (response != undefined && response != null) {

                                item["M_LetB_1"] = response["M_LetB_1"] == undefined ? "" : response["M_LetB_1"];
                                item["MB_LetB_Rate_1"] = response["MB_LetB_Rate_1"] == undefined ? 0 : response["MB_LetB_Rate_1"];
                                item["TG_LetB_Rate_1"] = response["TG_LetB_Rate_1"] == undefined ? 0 : response["TG_LetB_Rate_1"];
                                item["MB_Dime_1"] = response["MB_Dime_1"] == undefined ? "" : response["MB_Dime_1"];
                                item["TG_Dime_1"] = response["TG_Dime_1"] == undefined ? "" : response["TG_Dime_1"];
                                item["MB_Dime_Rate_1"] = response["MB_Dime_Rate_1"] == undefined ? 0 : response["MB_Dime_Rate_1"];
                                item["TG_Dime_Rate_1"] = response["TG_Dime_Rate_1"] == undefined ? 0 : response["TG_Dime_Rate_1"];
                                item["M_LetB_2"] = response["M_LetB_2"] == undefined ? "" : response["M_LetB_2"];
                                item["MB_LetB_Rate_2"] = response["MB_LetB_Rate_2"] == undefined ? 0 : response["MB_LetB_Rate_2"];
                                item["TG_LetB_Rate_2"] = response["TG_LetB_Rate_2"] == undefined ? 0 : response["TG_LetB_Rate_2"];
                                item["MB_Dime_2"] = response["MB_Dime_2"] == undefined ? "" : response["MB_Dime_2"];
                                item["TG_Dime_2"] = response["TG_Dime_2"] == undefined ? "" : response["TG_Dime_2"];
                                item["MB_Dime_Rate_2"] = response["MB_Dime_Rate_2"] == undefined ? 0 : response["MB_Dime_Rate_2"];
                                item["TG_Dime_Rate_2"] = response["TG_Dime_Rate_2"] == undefined ? 0 : response["TG_Dime_Rate_2"];
                                item["M_LetB_3"] = response["M_LetB_3"] == undefined ? "" : response["M_LetB_3"];
                                item["MB_LetB_Rate_3"] = response["MB_LetB_Rate_3"] == undefined ? 0 : response["MB_LetB_Rate_3"];
                                item["TG_LetB_Rate_3"] = response["TG_LetB_Rate_3"] == undefined ? 0 : response["TG_LetB_Rate_3"];
                                item["MB_Dime_3"] = response["MB_Dime_3"] == undefined ? "" : response["MB_Dime_3"];
                                item["TG_Dime_3"] = response["TG_Dime_3"] == undefined ? "" : response["TG_Dime_3"];
                                item["MB_Dime_Rate_3"] = response["MB_Dime_Rate_3"] == undefined ? 0 : response["MB_Dime_Rate_3"];
                                item["TG_Dime_Rate_3"] = response["TG_Dime_Rate_3"] == undefined ? 0 : response["TG_Dime_Rate_3"];

                            } else {

                                item["M_LetB_1"] = "";
                                item["MB_LetB_Rate_1"] = 0;
                                item["TG_LetB_Rate_1"] = 0;
                                item["MB_Dime_1"] = "";
                                item["TG_Dime_1"] = "";
                                item["MB_Dime_Rate_1"] = 0;
                                item["TG_Dime_Rate_1"] = 0;
                                item["M_LetB_2"] = "";
                                item["MB_LetB_Rate_2"] = 0;
                                item["TG_LetB_Rate_2"] = 0;
                                item["MB_Dime_2"] = "";
                                item["TG_Dime_2"] = "";
                                item["MB_Dime_Rate_2"] = 0;
                                item["TG_Dime_Rate_2"] = 0;
                                item["M_LetB_3"] = "";
                                item["MB_LetB_Rate_3"] = 0;
                                item["TG_LetB_Rate_3"] = 0;
                                item["MB_Dime_3"] = "";
                                item["TG_Dime_3"] = "";
                                item["MB_Dime_Rate_3"] = 0;
                                item["TG_Dime_Rate_3"] = 0;

                            }

                        }
                    }


                    if (item["CORNER"] === 1 && item["showType"] === "rb") {

                        let response = await getFT_CORNER_INPLAY(thirdPartyAuthData, {ecid: item["ECID"]});

                        if (response != undefined && response != null) {

                            await dispatchFT_CORNER_INPLAY(response);

                            item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];
                            item["MB_Dime_RB_CN"] = response["MB_Dime_RB"] == undefined ? "" : response["MB_Dime_RB"];
                            item["TG_Dime_RB_CN"] = response["TG_Dime_RB"] == undefined ? "" : response["TG_Dime_RB"];
                            item["MB_Dime_Rate_RB_CN"] = response["MB_Dime_Rate_RB"] == undefined ? 0 : response["MB_Dime_Rate_RB"];
                            item["TG_Dime_Rate_RB_CN"] = response["TG_Dime_Rate_RB"] == undefined ? 0 : response["TG_Dime_Rate_RB"];
                            item["MB_Dime_RB_H_CN"] = response["MB_Dime_RB_H"] == undefined ? "" : response["MB_Dime_RB_H"];
                            item["TG_Dime_RB_H_CN"] = response["TG_Dime_RB_H"] == undefined ? "" : response["TG_Dime_RB_H"];
                            item["MB_Dime_Rate_RB_H_CN"] = response["MB_Dime_Rate_RB_H"] == undefined ? 0 : response["MB_Dime_Rate_RB_H"];
                            item["TG_Dime_Rate_RB_H_CN"] = response["TG_Dime_Rate_RB_H"] == undefined ? 0 : response["TG_Dime_Rate_RB_H"];
                            item["S_Single_Rate_CN"] = response["S_Single_Rate"] == undefined ? 0 : response["S_Single_Rate"];
                            item["S_Double_Rate_CN"] = response["S_Double_Rate"] == undefined ? 0 : response["S_Double_Rate"];
                            item["S_Single_Rate_H_CN"] = response["S_Single_Rate_H"] == undefined ? 0 : response["S_Single_Rate_H"];
                            item["S_Double_Rate_H_CN"] = response["S_Double_Rate_H"] == undefined ? 0 : response["S_Double_Rate_H"];

                        } else {

                            item["CN_MID"] = response["MID"];
                            item["MB_Dime_RB_CN"] = "";
                            item["TG_Dime_RB_CN"] = "";
                            item["MB_Dime_Rate_RB_CN"] = 0;
                            item["TG_Dime_Rate_RB_CN"] = 0;
                            item["MB_Dime_RB_H_CN"] = "";
                            item["TG_Dime_RB_H_CN"] = "";
                            item["MB_Dime_Rate_RB_H_CN"] = 0;
                            item["TG_Dime_Rate_RB_H_CN"] = 0;
                            item["S_Single_Rate_CN"] = 0;
                            item["S_Double_Rate_CN"] = 0;
                            item["S_Single_Rate_H_CN"] = 0;
                            item["S_Double_Rate_H_CN"] = 0;

                        }

                    } else if (item["CORNER"] === 1 && item["showType"] === "ft") {

                        let response = await getFT_CORNER_TODAY(thirdPartyAuthData, {ecid: item["ECID"]});

                        if (response != undefined && response != null) {

                            await dispatchFT_CORNER_TODAY(response);

                            item["CN_MID"] = response["MID"] == undefined ? "" : response["MID"];

                            item["M_LetB_CN"] = response["M_LetB"] == undefined ? "" : response["M_LetB"];
                            item["MB_LetB_Rate_CN"] = response["MB_LetB_Rate"] == undefined ? 0 : response["MB_LetB_Rate"];
                            item["TG_LetB_Rate_CN"] = response["TG_LetB_Rate"] == undefined ? 0 : response["TG_LetB_Rate"];

                            item["MB_Dime_CN"] = response["MB_Dime"] == undefined ? "" : response["MB_Dime"];
                            item["TG_Dime_CN"] = response["TG_Dime"] == undefined ? "" : response["TG_Dime"];
                            item["MB_Dime_Rate_CN"] = response["MB_Dime_Rate"] == undefined ? 0 : response["MB_Dime_Rate"];
                            item["TG_Dime_Rate_CN"] = response["TG_Dime_Rate"] == undefined ? 0 : response["TG_Dime_Rate"];

                            item["M_Flat_Rate_CN"] = response["M_Flat_Rate"] == undefined ? "" : response["M_Flat_Rate"];
                            item["MB_Win_Rate_CN"] = response["MB_Win_Rate"] == undefined ? 0 : response["MB_Win_Rate"];
                            item["TG_Win_Rate_CN"] = response["TG_Win_Rate"] == undefined ? 0 : response["TG_Win_Rate"];

                        } else {                        

                            item["CN_MID"] = "";

                            item["M_LetB_CN"] = "";
                            item["MB_LetB_Rate_CN"] = 0;
                            item["TG_LetB_Rate_CN"] = 0;

                            item["MB_Dime_CN"] = "";
                            item["TG_Dime_CN"] = "";
                            item["MB_Dime_Rate_CN"] = 0;
                            item["TG_Dime_Rate_CN"] = 0;

                            item["M_Flat_Rate_CN"] = 0;
                            item["MB_Win_Rate_CN"] = 0;
                            item["TG_Win_Rate_CN"] = 0;
                        }

                    }

                    if (item["LID"] !== "" && item["ECID"] !== "" && item["showType"] == "ft") {


                        let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});

                        // console.log(moreData);

                        if (moreData !== undefined && moreData != null && moreData != {}) {
                                
                            item["MBMB"] = moreData["MBMB"] == undefined ? 0 : moreData["MBMB"];
                            item["MBFT"] = moreData["MBFT"] == undefined ? 0 : moreData["MBFT"];
                            item["MBTG"] = moreData["MBTG"] == undefined ? 0 : moreData["MBTG"];
                            item["FTMB"] = moreData["FTMB"] == undefined ? 0 : moreData["FTMB"];
                            item["FTFT"] = moreData["FTFT"] == undefined ? 0 : moreData["FTFT"];
                            item["FTTG"] = moreData["FTTG"] == undefined ? 0 : moreData["FTTG"];
                            item["TGMB"] = moreData["TGMB"] == undefined ? 0 : moreData["TGMB"];
                            item["TGTG"] = moreData["TGTG"] == undefined ? 0 : moreData["TGTG"];
                            item["TGFT"] = moreData["TGFT"] == undefined ? 0 : moreData["TGFT"];
                            item["F_Show"] = moreData["F_Show"] == undefined ? 0 : moreData["F_Show"];

                            item["S_0_1"] = moreData["S_0_1"] == undefined ? 0 : moreData["S_0_1"];
                            item["S_2_3"] = moreData["S_2_3"] == undefined ? 0 : moreData["S_2_3"];
                            item["S_4_6"] = moreData["S_4_6"] == undefined ? 0 : moreData["S_4_6"];
                            item["S_7UP"] = moreData["S_7UP"] == undefined ? 0 : moreData["S_7UP"];
                            item["T_Show"] = moreData["T_Show"] == undefined ? 0 : moreData["T_Show"];

                        } else {
                            item["MBMB"] = 0 ;
                            item["MBFT"] = 0;
                            item["MBTG"] = 0;
                            item["FTMB"] = 0;
                            item["FTFT"] = 0;
                            item["FTTG"] = 0;
                            item["TGMB"] = 0;
                            item["TGTG"] = 0;
                            item["TGFT"] = 0;
                            item["F_Show"] = 0;

                            item["S_0_1"] = 0;
                            item["S_2_3"] = 0;
                            item["S_4_6"] = 0;
                            item["S_7UP"] = 0;
                            item["T_Show"] = 0;
                        }
                        
                    }
                }));

                dispatchFT_MAIN_FAVORITE(itemList)            
            }

            io.emit("receivedFTFavoriteMessage", itemList);

        }, 300000);
    })

    socket.on("stopFTFavoriteMessage", () => {
        clearInterval(leagueParlayInterval);
        clearInterval(ftParlayInterval);
        clearInterval(obtIterval);
    })

    //=============== League Today Data ======================//

    socket.on("sendLeagueTodayMessage", async () => {
        console.log("sendLeagueTodayMessage");
        let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
        console.log("11111111111111111111111", result);
        io.emit("receivedLeagueTodayMessage", result)
        leagueTodayInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
            io.emit("receivedLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueTodayMessage", () => {
        clearInterval(leagueTodayInterval);
    })

    //======================== League Early Data ======================//

    socket.on("sendLeagueEarlyMessage", async () => {
        // console.log("sendLeagueEarlyMessage");
        clearInterval(leagueTodayInterval);
        let result = await getFT_LEAGUE_EARLY(thirdPartyAuthData);
        io.emit("receivedLeagueTodayMessage", result)
        leagueEarlyInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_EARLY(thirdPartyAuthData);
            io.emit("receivedLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueEarlyMessage", () => {
        clearInterval(leagueEarlyInterval);
    })

    //===================== League Champion Data ======================//

    socket.on("sendLeagueChampionMessage", async () => {
        // console.log("sendLeagueChampionMessage");
        let result = await getFT_LEAGUE_CHAMPION(thirdPartyAuthData);
        io.emit("receivedLeagueChampionMessage", result)
        leagueChampionInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_CHAMPION(thirdPartyAuthData);
            io.emit("receivedLeagueChampionMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueChampionMessage", () => {
        clearInterval(leagueChampionInterval);
    })

    //===================== League Parlay Data ======================//

    socket.on("sendLeagueParlayMessage", async () => {
        // console.log("sendLeagueParlayMessage");
        let result = await getFT_LEAGUE_PARLAY(thirdPartyAuthData);
        io.emit("receivedLeagueParlayMessage", result)
        leagueParlayInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_PARLAY(thirdPartyAuthData);
            io.emit("receivedLeagueParlayMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueParlayMessage", () => {
        clearInterval(leagueParlayInterval);
    })

    //=============== FT Today Correct Score Data ======================//

    socket.on('sendCorrectScoreToday', async (data) => {
        clearInterval(leagueTodayInterval);
        clearInterval(ftTodayInterval);
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_TODAY(thirdPartyAuthData, data);
        if (itemList && itemList.length > 0) {
            await Promise.all(itemList.map(async item => {

                if (item["LID"] !== "" && item["ECID"] !== "") {

                    let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});
                    // console.log(moreData);

                    if (moreData != undefined) {

                        item["MB1TG0H"] = moreData["MB1TG0H"] == undefined ? 0 : moreData["MB1TG0H"];
                        item["MB2TG0H"] = moreData["MB2TG0H"] == undefined ? 0 : moreData["MB2TG0H"];
                        item["MB2TG1H"] = moreData["MB2TG1H"] == undefined ? 0 : moreData["MB2TG1H"];
                        item["MB3TG0H"] = moreData["MB3TG0H"] == undefined ? 0 : moreData["MB3TG0H"];
                        item["MB3TG1H"] = moreData["MB3TG1H"] == undefined ? 0 : moreData["MB3TG1H"];
                        item["MB3TG2H"] = moreData["MB3TG2H"] == undefined ? 0 : moreData["MB3TG2H"];
                        item["MB4TG0H"] = moreData["MB4TG0H"] == undefined ? 0 : moreData["MB4TG0H"];
                        item["MB4TG1H"] = moreData["MB4TG1H"] == undefined ? 0 : moreData["MB4TG1H"];
                        item["MB4TG2H"] = moreData["MB4TG2H"] == undefined ? 0 : moreData["MB4TG2H"];
                        item["MB4TG3H"] = moreData["MB4TG3H"] == undefined ? 0 : moreData["MB4TG3H"];
                        item["MB0TG0H"] = moreData["MB0TG0H"] == undefined ? 0 : moreData["MB0TG0H"];
                        item["MB1TG1H"] = moreData["MB1TG1H"] == undefined ? 0 : moreData["MB1TG1H"];
                        item["MB2TG2H"] = moreData["MB2TG2H"] == undefined ? 0 : moreData["MB2TG2H"];
                        item["MB3TG3H"] = moreData["MB3TG3H"] == undefined ? 0 : moreData["MB3TG3H"];
                        item["MB4TG4H"] = moreData["MB4TG4H"] == undefined ? 0 : moreData["MB4TG4H"];
                        item["MB0TG1H"] = moreData["MB0TG1H"] == undefined ? 0 : moreData["MB0TG1H"];
                        item["MB0TG2H"] = moreData["MB0TG2H"] == undefined ? 0 : moreData["MB0TG2H"];
                        item["MB1TG2H"] = moreData["MB1TG2H"] == undefined ? 0 : moreData["MB1TG2H"];
                        item["MB0TG3H"] = moreData["MB0TG3H"] == undefined ? 0 : moreData["MB0TG3H"];
                        item["MB1TG3H"] = moreData["MB1TG3H"] == undefined ? 0 : moreData["MB1TG3H"];
                        item["MB2TG3H"] = moreData["MB2TG3H"] == undefined ? 0 : moreData["MB2TG3H"];
                        item["MB0TG4H"] = moreData["MB0TG4H"] == undefined ? 0 : moreData["MB0TG4H"];
                        item["MB1TG4H"] = moreData["MB1TG4H"] == undefined ? 0 : moreData["MB1TG4H"];
                        item["MB2TG4H"] = moreData["MB2TG4H"] == undefined ? 0 : moreData["MB2TG4H"];
                        item["MB3TG4H"] = moreData["MB3TG4H"] == undefined ? 0 : moreData["MB3TG4H"];
                        item["UP5H"] = moreData["UP5H"] == undefined ? 0 : moreData["UP5H"];
                        item["HPD_Show"] = moreData["HPD_Show"] == undefined ? 0 : moreData["HPD_Show"];

                    } else {

                        item["MB1TG0H"] = 0;
                        item["MB2TG0H"] = 0;
                        item["MB2TG1H"] = 0;
                        item["MB3TG0H"] = 0;
                        item["MB3TG1H"] = 0;
                        item["MB3TG2H"] = 0;
                        item["MB4TG0H"] = 0;
                        item["MB4TG1H"] = 0;
                        item["MB4TG2H"] = 0;
                        item["MB4TG3H"] = 0;
                        item["MB0TG0H"] = 0;
                        item["MB1TG1H"] = 0;
                        item["MB2TG2H"] = 0;
                        item["MB3TG3H"] = 0;
                        item["MB4TG4H"] = 0;
                        item["MB0TG1H"] = 0;
                        item["MB0TG2H"] = 0;
                        item["MB1TG2H"] = 0;
                        item["MB0TG3H"] = 0;
                        item["MB1TG3H"] = 0;
                        item["MB2TG3H"] = 0;
                        item["MB0TG4H"] = 0;
                        item["MB1TG4H"] = 0;
                        item["MB2TG4H"] = 0;
                        item["MB3TG4H"] = 0;
                        item["UP5H"] = 0;
                        item["HPD_Show"] = 0;

                    }

                }

            }));
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        io.emit("receivedFTTodayScoreData", itemList);
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_TODAY(thirdPartyAuthData, data);
            io.emit("receivedFTTodayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                await Promise.all(itemList.map(async item => {

                    if (item["LID"] !== "" && item["ECID"] !== "") {

                        let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});

                        if (moreData != undefined) {

                            item["MB1TG0H"] = moreData["MB1TG0H"] == undefined ? 0 : moreData["MB1TG0H"];
                            item["MB2TG0H"] = moreData["MB2TG0H"] == undefined ? 0 : moreData["MB2TG0H"];
                            item["MB2TG1H"] = moreData["MB2TG1H"] == undefined ? 0 : moreData["MB2TG1H"];
                            item["MB3TG0H"] = moreData["MB3TG0H"] == undefined ? 0 : moreData["MB3TG0H"];
                            item["MB3TG1H"] = moreData["MB3TG1H"] == undefined ? 0 : moreData["MB3TG1H"];
                            item["MB3TG2H"] = moreData["MB3TG2H"] == undefined ? 0 : moreData["MB3TG2H"];
                            item["MB4TG0H"] = moreData["MB4TG0H"] == undefined ? 0 : moreData["MB4TG0H"];
                            item["MB4TG1H"] = moreData["MB4TG1H"] == undefined ? 0 : moreData["MB4TG1H"];
                            item["MB4TG2H"] = moreData["MB4TG2H"] == undefined ? 0 : moreData["MB4TG2H"];
                            item["MB4TG3H"] = moreData["MB4TG3H"] == undefined ? 0 : moreData["MB4TG3H"];
                            item["MB0TG0H"] = moreData["MB0TG0H"] == undefined ? 0 : moreData["MB0TG0H"];
                            item["MB1TG1H"] = moreData["MB1TG1H"] == undefined ? 0 : moreData["MB1TG1H"];
                            item["MB2TG2H"] = moreData["MB2TG2H"] == undefined ? 0 : moreData["MB2TG2H"];
                            item["MB3TG3H"] = moreData["MB3TG3H"] == undefined ? 0 : moreData["MB3TG3H"];
                            item["MB4TG4H"] = moreData["MB4TG4H"] == undefined ? 0 : moreData["MB4TG4H"];
                            item["MB0TG1H"] = moreData["MB0TG1H"] == undefined ? 0 : moreData["MB0TG1H"];
                            item["MB0TG2H"] = moreData["MB0TG2H"] == undefined ? 0 : moreData["MB0TG2H"];
                            item["MB1TG2H"] = moreData["MB1TG2H"] == undefined ? 0 : moreData["MB1TG2H"];
                            item["MB0TG3H"] = moreData["MB0TG3H"] == undefined ? 0 : moreData["MB0TG3H"];
                            item["MB1TG3H"] = moreData["MB1TG3H"] == undefined ? 0 : moreData["MB1TG3H"];
                            item["MB2TG3H"] = moreData["MB2TG3H"] == undefined ? 0 : moreData["MB2TG3H"];
                            item["MB0TG4H"] = moreData["MB0TG4H"] == undefined ? 0 : moreData["MB0TG4H"];
                            item["MB1TG4H"] = moreData["MB1TG4H"] == undefined ? 0 : moreData["MB1TG4H"];
                            item["MB2TG4H"] = moreData["MB2TG4H"] == undefined ? 0 : moreData["MB2TG4H"];
                            item["MB3TG4H"] = moreData["MB3TG4H"] == undefined ? 0 : moreData["MB3TG4H"];
                            item["UP5H"] = moreData["UP5H"] == undefined ? 0 : moreData["UP5H"];
                            item["HPD_Show"] = moreData["HPD_Show"] == undefined ? 0 : moreData["HPD_Show"];

                        } else {

                            item["MB1TG0H"] = 0;
                            item["MB2TG0H"] = 0;
                            item["MB2TG1H"] = 0;
                            item["MB3TG0H"] = 0;
                            item["MB3TG1H"] = 0;
                            item["MB3TG2H"] = 0;
                            item["MB4TG0H"] = 0;
                            item["MB4TG1H"] = 0;
                            item["MB4TG2H"] = 0;
                            item["MB4TG3H"] = 0;
                            item["MB0TG0H"] = 0;
                            item["MB1TG1H"] = 0;
                            item["MB2TG2H"] = 0;
                            item["MB3TG3H"] = 0;
                            item["MB4TG4H"] = 0;
                            item["MB0TG1H"] = 0;
                            item["MB0TG2H"] = 0;
                            item["MB1TG2H"] = 0;
                            item["MB0TG3H"] = 0;
                            item["MB1TG3H"] = 0;
                            item["MB2TG3H"] = 0;
                            item["MB0TG4H"] = 0;
                            item["MB1TG4H"] = 0;
                            item["MB2TG4H"] = 0;
                            item["MB3TG4H"] = 0;
                            item["UP5H"] = 0;
                            item["HPD_Show"] = 0;

                        }

                    }

                }));
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
            io.emit("receivedFTTodayScoreData", itemList);
        }, 30000)
    });

    socket.on("stopCorrectScoreToday", () => {
        clearInterval(correctScoreInterval);
    })

    //=============== FT Early Correct Score Data ======================//

    socket.on('sendCorrectScoreEarly', async (data) => {
        clearInterval(leagueEarlyInterval);
        clearInterval(ftEarlyInterval);
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_EARLY(thirdPartyAuthData, data);
        io.emit("receivedFTEarlyScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_EARLY(thirdPartyAuthData, data);
            io.emit("receivedFTEarlyScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreEarly", () => {
        clearInterval(correctScoreInterval);
    })


    //=============== FT Parlay Correct Score Data ======================//

    socket.on('sendCorrectScoreParlay', async (data) => {
        // console.log('sendCorrectScoreParlay');
        clearInterval(leagueParlayInterval);
        clearInterval(ftParlayInterval);
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_PARLAY(thirdPartyAuthData, data);
        io.emit("receivedFTParlayScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_PARLAY(thirdPartyAuthData, data);
            io.emit("receivedFTParlayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreParlay", () => {
        clearInterval(correctScoreInterval);
    })

    //=============== FT Favorite Correct Score Data ======================//

    socket.on('sendCorrectScoreFavorite', async (data) => {
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_FAVORITE(thirdPartyAuthData, data);
        io.emit("receivedFTFavoriteScoreData", itemList);
        if (itemList && itemList.length > 0) {
            await Promise.all(itemList.map(async item => {
               if (item["LID"] !== "" && item["ECID"] !== "" && item["showType"] == "ft") {

                    let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});
                    // console.log(moreData);

                    item["MB1TG0H"] = moreData["MB1TG0H"] == undefined ? 0 : moreData["MB1TG0H"];
                    item["MB2TG0H"] = moreData["MB2TG0H"] == undefined ? 0 : moreData["MB2TG0H"];
                    item["MB2TG1H"] = moreData["MB2TG1H"] == undefined ? 0 : moreData["MB2TG1H"];
                    item["MB3TG0H"] = moreData["MB3TG0H"] == undefined ? 0 : moreData["MB3TG0H"];
                    item["MB3TG1H"] = moreData["MB3TG1H"] == undefined ? 0 : moreData["MB3TG1H"];
                    item["MB3TG2H"] = moreData["MB3TG2H"] == undefined ? 0 : moreData["MB3TG2H"];
                    item["MB4TG0H"] = moreData["MB4TG0H"] == undefined ? 0 : moreData["MB4TG0H"];
                    item["MB4TG1H"] = moreData["MB4TG1H"] == undefined ? 0 : moreData["MB4TG1H"];
                    item["MB4TG2H"] = moreData["MB4TG2H"] == undefined ? 0 : moreData["MB4TG2H"];
                    item["MB4TG3H"] = moreData["MB4TG3H"] == undefined ? 0 : moreData["MB4TG3H"];
                    item["MB0TG0H"] = moreData["MB0TG0H"] == undefined ? 0 : moreData["MB0TG0H"];
                    item["MB1TG1H"] = moreData["MB1TG1H"] == undefined ? 0 : moreData["MB1TG1H"];
                    item["MB2TG2H"] = moreData["MB2TG2H"] == undefined ? 0 : moreData["MB2TG2H"];
                    item["MB3TG3H"] = moreData["MB3TG3H"] == undefined ? 0 : moreData["MB3TG3H"];
                    item["MB4TG4H"] = moreData["MB4TG4H"] == undefined ? 0 : moreData["MB4TG4H"];
                    item["MB0TG1H"] = moreData["MB0TG1H"] == undefined ? 0 : moreData["MB0TG1H"];
                    item["MB0TG2H"] = moreData["MB0TG2H"] == undefined ? 0 : moreData["MB0TG2H"];
                    item["MB1TG2H"] = moreData["MB1TG2H"] == undefined ? 0 : moreData["MB1TG2H"];
                    item["MB0TG3H"] = moreData["MB0TG3H"] == undefined ? 0 : moreData["MB0TG3H"];
                    item["MB1TG3H"] = moreData["MB1TG3H"] == undefined ? 0 : moreData["MB1TG3H"];
                    item["MB2TG3H"] = moreData["MB2TG3H"] == undefined ? 0 : moreData["MB2TG3H"];
                    item["MB0TG4H"] = moreData["MB0TG4H"] == undefined ? 0 : moreData["MB0TG4H"];
                    item["MB1TG4H"] = moreData["MB1TG4H"] == undefined ? 0 : moreData["MB1TG4H"];
                    item["MB2TG4H"] = moreData["MB2TG4H"] == undefined ? 0 : moreData["MB2TG4H"];
                    item["MB3TG4H"] = moreData["MB3TG4H"] == undefined ? 0 : moreData["MB3TG4H"];
                    item["UP5H"] = moreData["UP5H"] == undefined ? 0 : moreData["UP5H"];
                    item["HPD_Show"] = moreData["HPD_Show"] == undefined ? 0 : moreData["HPD_Show"];

                }            
            }));
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_FAVORITE(thirdPartyAuthData, data);
            io.emit("receivedFTFavoriteScoreData", itemList);
            if (itemList && itemList.length > 0) {
                await Promise.all(itemList.map(async item => {
                    if (item["LID"] !== "" && item["ECID"] !== "" && item["showType"] == "ft") {

                        let moreData = await getFT_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], ecid: item["ECID"], showtype: "today"});
                        // console.log(moreData);

                        item["MB1TG0H"] = moreData["MB1TG0H"] == undefined ? 0 : moreData["MB1TG0H"];
                        item["MB2TG0H"] = moreData["MB2TG0H"] == undefined ? 0 : moreData["MB2TG0H"];
                        item["MB2TG1H"] = moreData["MB2TG1H"] == undefined ? 0 : moreData["MB2TG1H"];
                        item["MB3TG0H"] = moreData["MB3TG0H"] == undefined ? 0 : moreData["MB3TG0H"];
                        item["MB3TG1H"] = moreData["MB3TG1H"] == undefined ? 0 : moreData["MB3TG1H"];
                        item["MB3TG2H"] = moreData["MB3TG2H"] == undefined ? 0 : moreData["MB3TG2H"];
                        item["MB4TG0H"] = moreData["MB4TG0H"] == undefined ? 0 : moreData["MB4TG0H"];
                        item["MB4TG1H"] = moreData["MB4TG1H"] == undefined ? 0 : moreData["MB4TG1H"];
                        item["MB4TG2H"] = moreData["MB4TG2H"] == undefined ? 0 : moreData["MB4TG2H"];
                        item["MB4TG3H"] = moreData["MB4TG3H"] == undefined ? 0 : moreData["MB4TG3H"];
                        item["MB0TG0H"] = moreData["MB0TG0H"] == undefined ? 0 : moreData["MB0TG0H"];
                        item["MB1TG1H"] = moreData["MB1TG1H"] == undefined ? 0 : moreData["MB1TG1H"];
                        item["MB2TG2H"] = moreData["MB2TG2H"] == undefined ? 0 : moreData["MB2TG2H"];
                        item["MB3TG3H"] = moreData["MB3TG3H"] == undefined ? 0 : moreData["MB3TG3H"];
                        item["MB4TG4H"] = moreData["MB4TG4H"] == undefined ? 0 : moreData["MB4TG4H"];
                        item["MB0TG1H"] = moreData["MB0TG1H"] == undefined ? 0 : moreData["MB0TG1H"];
                        item["MB0TG2H"] = moreData["MB0TG2H"] == undefined ? 0 : moreData["MB0TG2H"];
                        item["MB1TG2H"] = moreData["MB1TG2H"] == undefined ? 0 : moreData["MB1TG2H"];
                        item["MB0TG3H"] = moreData["MB0TG3H"] == undefined ? 0 : moreData["MB0TG3H"];
                        item["MB1TG3H"] = moreData["MB1TG3H"] == undefined ? 0 : moreData["MB1TG3H"];
                        item["MB2TG3H"] = moreData["MB2TG3H"] == undefined ? 0 : moreData["MB2TG3H"];
                        item["MB0TG4H"] = moreData["MB0TG4H"] == undefined ? 0 : moreData["MB0TG4H"];
                        item["MB1TG4H"] = moreData["MB1TG4H"] == undefined ? 0 : moreData["MB1TG4H"];
                        item["MB2TG4H"] = moreData["MB2TG4H"] == undefined ? 0 : moreData["MB2TG4H"];
                        item["MB3TG4H"] = moreData["MB3TG4H"] == undefined ? 0 : moreData["MB3TG4H"];
                        item["UP5H"] = moreData["UP5H"] == undefined ? 0 : moreData["UP5H"];
                        item["HPD_Show"] = moreData["HPD_Show"] == undefined ? 0 : moreData["HPD_Show"];

                    }

                }));

                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }

        }, 30000)

    });

    socket.on("stopCorrectScoreFavorite", () => {
        clearInterval(correctScoreInterval);
    })

    // ========================= BK Inplay ========================= //

    socket.on('sendBKInPlayMessage', () => {
        // console.log(bkInPlayList);
        io.emit("receivedBKInPlayData", bkInPlayList);
    });

    //=============== BK Today League Data ======================//

    socket.on("sendBKLeagueTodayMessage", async () => {
        // console.log("sendBKLeagueTodayMessage");
        let result = await getBK_LEAGUE_TODAY(thirdPartyAuthData);
        io.emit("receivedBKLeagueTodayMessage", result)
        leagueBKTodayInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_TODAY(thirdPartyAuthData);
            io.emit("receivedBKLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueTodayMessage", () => {
        clearInterval(leagueBKTodayInterval);
    })

    //=============== BK Today Data ======================//

    socket.on("sendBKTodayMessage", async (data) => {

        let itemList = await getBK_MAIN_TODAY(thirdPartyAuthData, data);

        let newItemList = [];

        if (itemList && itemList.length > 0) {

            await Promise.all(itemList.map(async item => {

                if (item["LID"] !== "" && item["MID"] !== "") {

                    let moreData = await getBK_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "today", flag_class: item["FLAG_CLASS"]});

                    // console.log(moreData);
                    if (moreData != undefined && moreData.length > 0) {

                        newItemList = [...newItemList, ...moreData];

                    }

                }

            }));

        }

        if (newItemList.length > 0) {

            // console.log(newItemList);

            dispatchBK_MAIN_TODAY(newItemList)

            io.emit("receivedBKTodayMessage", newItemList);

        } else {

            dispatchBK_MAIN_TODAY(itemList)

            io.emit("receivedBKTodayMessage", itemList);
            
        }

        bkTodayInterval = setInterval(async () => {

            let itemList = await getBK_MAIN_TODAY(thirdPartyAuthData, data);

            let newItemList = [];

            if (itemList && itemList.length > 0) {

                await Promise.all(itemList.map(async item => {

                    if (item["LID"] !== "" && item["MID"] !== "") {

                        let moreData = await getBK_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "today", flag_class: item["FLAG_CLASS"]});

                        // console.log(moreData);
                        if (moreData != undefined && moreData.length > 0) {

                            newItemList = [...newItemList, ...moreData];

                        }

                    }

                }));

            }

            if (newItemList.length > 0) {

                // console.log(newItemList);

                dispatchBK_MAIN_TODAY(newItemList)

                io.emit("receivedBKTodayMessage", newItemList);

            } else {

                dispatchBK_MAIN_TODAY(itemList)

                io.emit("receivedBKTodayMessage", itemList);
                
            }
        }, 300000);
    })

    socket.on("stopBKTodayMessage", () => {
        clearInterval(bkTodayInterval);
    })

    //=============== BK Early League Data ======================//

    socket.on("sendBKLeagueEarlyMessage", async () => {
        // console.log("sendBKLeagueEarlyMessage");
        let result = await getBK_LEAGUE_EARLY(thirdPartyAuthData);
        io.emit("receivedBKLeagueEarlyMessage", result)
        leagueBKEarlyInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_EARLY(thirdPartyAuthData);
            io.emit("receivedBKLeagueEarlyMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueEarlyMessage", () => {
        clearInterval(leagueBKEarlyInterval);
    })



    //=============== BK Early Data ======================//

    socket.on("sendBKEarlyMessage", async (data) => {
        let itemList = await getBK_MAIN_EARLY(thirdPartyAuthData, data);
        io.emit("receivedBKEarlyMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchBK_MAIN_TODAY(itemList)            
        }
        bkEarlyInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_EARLY(thirdPartyAuthData, data);
            io.emit("receivedBKEarlyMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchBK_MAIN_TODAY(itemList)            
            }
        }, 300000);
    })

    socket.on("stopBKEarlyMessage", () => {
        clearInterval(bkEarlyInterval);
    })

    //=============== BK Champion League Data ======================//

    socket.on("sendBKLeagueChampionMessage", async () => {
        // console.log("sendBKLeagueChampionMessage");
        let result = await getBK_LEAGUE_CHAMPION(thirdPartyAuthData);
        io.emit("receivedBKLeagueChampionMessage", result)
        leagueBKChampionInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_CHAMPION(thirdPartyAuthData);
            io.emit("receivedBKLeagueChampionMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueChampionMessage", () => {
        clearInterval(leagueBKChampionInterval);
    })

    //=============== BK Champion Data ======================//

    socket.on("sendBKChampionMainMessage", async (data) => {
        let itemList = await getBK_MAIN_CHAMPION(thirdPartyAuthData, data);
        // console.log(itemList)
        io.emit("receivedBKChampionMainMessage", itemList);
        if (Array.isArray(itemList) && itemList.length > 0) {
            dispatchBK_MAIN_CHAMPION(itemList) 
        } else if (itemList) {
            let tempItemList = [];
            tempItemList.push(itemList);
            dispatchBK_MAIN_CHAMPION(tempItemList)    
        }
        bkChampionInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_CHAMPION(thirdPartyAuthData, data);
            io.emit("receivedFTChampionMessage", itemList);
            if (Array.isArray(itemList) && itemList.length > 0) {
                dispatchBK_MAIN_CHAMPION(itemList) 
            } else if (itemList) {
                let tempItemList = [];
                tempItemList.push(itemList);
                dispatchBK_MAIN_CHAMPION(tempItemList)    
            }
        }, 300000);
    })

    socket.on("stopBKChampionMessage", () => {
        clearInterval(bkChampionInterval);
    })

    //=============== BK Parlay League Data ======================//

    socket.on("sendBKLeagueParlayMessage", async () => {
        // console.log("sendBKLeagueParlayMessage");
        let result = await getBK_LEAGUE_PARLAY(thirdPartyAuthData);
        io.emit("receivedBKLeagueParlayMessage", result)
        leagueBKParlayInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_PARLAY(thirdPartyAuthData);
            io.emit("receivedBKLeagueParlayMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueParlayMessage", () => {
        clearInterval(leagueBKParlayInterval);
    })

    //=============== BK Parlay Data ======================//

    socket.on("sendBKParlayMessage", async (data) => {

        let itemList = await getBK_MAIN_PARLAY(thirdPartyAuthData, data);

        let newItemList = [];

        if (itemList && itemList.length > 0) {

            await Promise.all(itemList.map(async item => {

                if (item["LID"] !== "" && item["MID"] !== "") {

                    let moreData = await getBK_MORE_PARLAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "today", flag_class: item["FLAG_CLASS"]});

                    // console.log(moreData);
                    if (moreData != undefined && moreData.length > 0) {

                        newItemList = [...newItemList, ...moreData];

                    }

                }

            }));

        }

        if (newItemList.length > 0) {

            // console.log(newItemList);

            dispatchBK_MAIN_PARLAY(newItemList)

            io.emit("receivedBKParlayMessage", newItemList);

        } else {

            dispatchBK_MAIN_PARLAY(itemList)

            io.emit("receivedBKParlayMessage", itemList);
            
        }
        bkParlayInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_PARLAY(thirdPartyAuthData, data);

            let newItemList = [];

            if (itemList && itemList.length > 0) {

                await Promise.all(itemList.map(async item => {

                    if (item["LID"] !== "" && item["MID"] !== "") {

                        let moreData = await getBK_MORE_PARLAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "today", flag_class: item["FLAG_CLASS"]});

                        // console.log(moreData);
                        if (moreData != undefined && moreData.length > 0) {

                            newItemList = [...newItemList, ...moreData];

                        }

                    }

                }));

            }

            if (newItemList.length > 0) {

                dispatchBK_MAIN_PARLAY(newItemList)

                io.emit("receivedBKParlayMessage", newItemList);

            } else {

                dispatchBK_MAIN_PARLAY(itemList)

                io.emit("receivedBKParlayMessage", itemList);
                
            }
        }, 300000);
    })

    socket.on("stopBKParlayMessage", () => {
        clearInterval(bkParlayInterval);
    })

    //=============== BK Favorite Data ======================//

    socket.on("sendBKFavoriteMessage", async (data) => {
        // console.log("sendBKFavoriteMessage: ", data);
        clearInterval(bkFavoriteInterval);

        let itemList = await getBK_MAIN_FAVORITE(thirdPartyAuthData, data);

        console.log(itemList);

        if (itemList && itemList.length > 0) {

            await Promise.all(itemList.map(async item => {

                if (item["LID"] !== "" && item["MID"] !== "") {

                    let moreData = [];

                    if (item["showType"] == "rb") {

                        moreData = await getBK_MORE_INPLAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "live", flag_class: item["FLAG_CLASS"]});

                        // console.log(moreData);

                    } else {

                        moreData = await getBK_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "today", flag_class: item["FLAG_CLASS"]});

                        // console.log(moreData);

                    }

                    moreData.map(moreItem => {
                        if (item["MID"] === moreItem["MID"]) {
                            item["S_Single_Rate"] = moreItem["S_Single_Rate"];
                            item["S_Double_Rate"] = moreItem["S_Double_Rate"];
                        }
                    })

                }

            }));

            dispatchBK_MAIN_FAVORITE(itemList);

            io.emit("receivedBKFavoriteMessage", itemList);
        }

        io.emit("receivedBKFavoriteMessage", itemList);

        bkFavoriteInterval = setInterval(async () => {

            let itemList = await getBK_MAIN_FAVORITE(thirdPartyAuthData, data);

            if (itemList && itemList.length > 0) {

                await Promise.all(itemList.map(async item => {

                    if (item["LID"] !== "" && item["MID"] !== "") {

                        let moreData = [];

                        if (item["showType"] == "rb") {

                            moreData = await getBK_MORE_INPLAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "live", flag_class: item["FLAG_CLASS"]});

                            // console.log(moreData);

                        } else {

                            moreData = await getBK_MORE_TODAY(thirdPartyAuthData, {lid: item["LID"], gid: item["MID"], showtype: "today", flag_class: item["FLAG_CLASS"]});

                            // console.log(moreData);

                        }

                        moreData.map(moreItem => {
                            if (item["MID"] === moreItem["MID"]) {
                                item["S_Single_Rate"] = moreItem["S_Single_Rate"];
                                item["S_Double_Rate"] = moreItem["S_Double_Rate"];
                            }
                        })

                    }

                }));

                dispatchBK_MAIN_FAVORITE(itemList)            
            }

            io.emit("receivedFTFavoriteMessage", itemList);

        }, 300000);

    })

    socket.on("stopBKFavoriteMessage", () => {
        clearInterval(bkFavoriteInterval);
    })

    // ==================== FT Inplay HDP_OU and Corner Data ================== //

    socket.on('sendHDP_OUData', async data => {
        // console.log(data);
        clearInterval(obtIterval);
        // clearInterval(ftInPlayInterval);


        // ftInPlayList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
        // io.emit("receivedFTInPlayData", ftInPlayList);
        // if (ftInPlayList && ftInPlayList.length > 0) {
        //     dispatchFT_FU_R_INPLAY(ftInPlayList)
        // }

        // ftInPlayInterval = setInterval(async () => {
        //     ftInPlayList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
        //     io.emit("receivedFTInPlayData", ftInPlayList);
        //     if (ftInPlayList && ftInPlayList.length > 0) {
        //         dispatchFT_FU_R_INPLAY(ftInPlayList)
        //     }
        // }, 20000);


        let item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
        // console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_Data", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        } 
        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_Data", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 20000);
    });

    socket.on('sendCornerData', async data => {
        // console.log(data);
        clearInterval(obtIterval);
        let item = await getFT_CORNER_INPLAY(thirdPartyAuthData, data);
        if (item) {
            io.emit("receivedFT_CORNER_Data", item);
            dispatchFT_CORNER_INPLAY(item);
        }
        obtIterval = setInterval( async () => {
            let item = await getFT_CORNER_INPLAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_CORNER_Data", item);
                dispatchFT_CORNER_INPLAY(item);
            }
        }, 8000)
    });

    //=============== FT Today HDP_OU Data ======================//

    socket.on('sendHDP_OU_TODAY', async (data, mainData) => {
        clearInterval(obtIterval);
        clearInterval(ftTodayInterval);

        let itemList = await getFT_DEFAULT_TODAY(thirdPartyAuthData, mainData);
        // console.log(itemList)
        io.emit("receivedFTTodayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_TODAY(itemList)            
        }
        ftTodayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_TODAY(thirdPartyAuthData, mainData);
            io.emit("receivedFTTodayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_TODAY(itemList)            
            }
        }, 300000);

        let item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
        // console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_TODAY", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_TODAY", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 30000);
    });


    //=============== FT Early HDP_OU Data ======================//

    socket.on('sendHDP_OU_EARLY', async (data, mainData) => {
        clearInterval(obtIterval);
        clearInterval(ftEarlyInterval);

        let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, mainData);
        // console.log(itemList)
        io.emit("receivedFTEarlyMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_TODAY(itemList)            
        }

        ftEarlyInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, mainData);
            io.emit("receivedFTEarlyMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_TODAY(itemList)            
            }
        }, 300000);

        let item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
        // console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_EARLY", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }

        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_EARLY", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 30000);
    });

    //=============== FT Parlay HDP_OU Data ======================//

    socket.on('sendHDP_OU_PARLAY', async (data, mainData) => {
        clearInterval(obtIterval);
        clearInterval(ftParlayInterval);

        let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, mainData);
        // console.log(itemList)
        io.emit("receivedFTParlayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_PARLAY(itemList)            
        }
        ftParlayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, mainData);
            io.emit("receivedFTParlayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_PARLAY(itemList)            
            }
        }, 300000);


        let item  = await getFT_HDP_OU_PARLAY(thirdPartyAuthData, data);
        // console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_PARLAY", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_PARLAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_PARLAY", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 30000);
    });

    //=============== FT Favorite HDP_OU Data ======================//

    socket.on('sendHDP_OU_FAVORITE', async (data, mainData) => {
        // console.log(data);
        clearInterval(obtIterval);
        clearInterval(ftParlayInterval);

        let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, mainData);
        // console.log(itemList)
        io.emit("receivedFTParlayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_PARLAY(itemList)            
        }
        ftParlayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, mainData);
            io.emit("receivedFTParlayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_PARLAY(itemList)            
            }
        }, 300000);


        let item;

        if (data["showtype"] === "rb") {
            item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
        }else {
            item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
        }
        // console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_FAVORITE", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item;
            if (data["showtype"] === "rb") {
                item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
            }else {
                item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
            }
            if (item) {
                io.emit("receivedFT_HDP_OU_FAVORITE", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 30000);
    });

    //=============== FT Today Corner Data ======================//

    socket.on('sendCornerToday', async data => {
        // console.log(data);
        clearInterval(obtIterval);
        let item = await getFT_CORNER_TODAY(thirdPartyAuthData, data);
        if (item) {
            io.emit("receivedFT_CORNER_TODAY", item);
            // dispatchFT_CORNER_TODAY(item);
        }
        obtIterval = setInterval( async () => {
            let item = await getFT_CORNER_TODAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_CORNER_TODAY", item);
                // dispatchFT_CORNER_TODAY(item);
            }
        }, 8000)
    });

});

app.get('/', (req, res) => {
    res.send("socket server");
});

server.listen(3000, () => {
    // console.log('listening on *:3000');
});;