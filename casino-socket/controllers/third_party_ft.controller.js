const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { SAVE_FT_CORRECT_SCORE } = require('../api');
const { GET_WEB_SYSTEM_DATA } = require('../api');
const { MATCH_SPORTS } = require('../api');
const { SAVE_FT_FU_R } = require('../api');
const { MATCH_CROWN } = require('../api');
const { SAVE_FT_IN_PLAY } = require('../api');
const { SAVE_FT_HDP_OBT } = require('../api');
const { SAVE_FT_CORNER_OBT } = require('../api');
const { SAVE_FT_CORNER_TODAY } = require('../api');
const { SAVE_FT_DEFAULT_PARLAY } = require('../api');
const { SAVE_FT_DEFAULT_TODAY } = require('../api');

var FormData = require('form-data');

const { XMLParser } = require("fast-xml-parser");

const options = {
    ignoreAttributes : false,
    attributeNamePrefix : ""
};

const parser = new XMLParser(options);


var moment = require('moment');


exports.getFT_MAIN_FAVORITE = async (thirdPartyAuthData, data) => {
	try {
		let ecidStr = "";
		for (let i = 0; i < data.length; i++) {
			if (i == data.length - 1) {
				ecidStr += data[i]["ecid"]
			} else {
				ecidStr += data[i]["ecid"] + "|"
			}
		}
		let itemList = [];
		let tempList = [];		
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		formData.append("date", "");
		formData.append("gtype", "ft");
		formData.append("showtype", "mygame");
		formData.append("rtype", "r");
		formData.append("ltype", 3);
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		formData.append("ecid_str", ecidStr);
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			var result = parser.parse(response.data);
			// console.log("FT_FAVORITE: ", result['serverresponse']['ec'])
			if (result['serverresponse']['code'] === "error") return null;
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			await Promise.all(tempList.map(async item => {
				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);
				if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
				}
				// console.log("22222222222222", item['game']['IOR_HREH'] ?? 0)
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				if (item['myGame'] === "rb") {
					let data = {
						showType: item['myGame'],
						Type: 'FT',
						Retime: item['game']['TIMER'],
						ECID: item['game']['ECID'],
						LID: item['game']['LID'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						MB_Team: item['game']['TEAM_H'],
						TG_Team: item['game']['TEAM_C'],
						MB_Team_tw: item['game']['TEAM_H'],
						TG_Team_tw: item['game']['TEAM_C'],
						MB_Team_en: item['game']['TEAM_H'],
						TG_Team_en: item['game']['TEAM_C'],
						M_League: item['game']['LEAGUE'],
						M_League_tw: item['game']['LEAGUE'],
						M_League_en: item['game']['LEAGUE'],
						MB_MID: item['game']['GNUM_H'],
						TG_MID: item['game']['GNUM_C'],
						ShowTypeRB: item['game']['STRONG'] == "" ? "H" : item['game']['STRONG'],
						M_LetB_RB: item['game']['RATIO_RE'] == "" ? 0 : item['game']['RATIO_RE'],
						MB_LetB_Rate_RB: item['game']['IOR_REH'] == "" ? 0 : item['game']['IOR_REH'],
						TG_LetB_Rate_RB: item['game']['IOR_REC'] == "" ? 0 : item['game']['IOR_REC'],
						MB_Dime_RB: item['game']['RATIO_ROUO'] == "" ? 0 : item['game']['RATIO_ROUO'],
						TG_Dime_RB: item['game']['RATIO_ROUU'] == "" ? 0 : item['game']['RATIO_ROUU'],
						MB_Dime_Rate_RB: item['game']['IOR_ROUC'] == "" ? 0 : item['game']['IOR_ROUC'],
						TG_Dime_Rate_RB: item['game']['IOR_ROUH'] == "" ? 0 : item['game']['IOR_ROUH'],
						ShowTypeHRB: item['game']['HSTRONG'] == "" ? "H" : item['game']['HSTRONG'],
						M_LetB_RB_H: item['game']['RATIO_HRE'] == "" ? 0 : item['game']['RATIO_HRE'],
						MB_LetB_Rate_RB_H: item['game']['IOR_HREH'] == "" ? 0 : item['game']['IOR_HREH'],
						TG_LetB_Rate_RB_H: item['game']['IOR_HREC'] == "" ? 0 : item['game']['IOR_HREC'],
						MB_Dime_RB_H: item['game']['RATIO_HROUO'] == "" ? 0 : item['game']['RATIO_HROUO'],
						TG_Dime_RB_H: item['game']['RATIO_HROUU'] == "" ? 0 : item['game']['RATIO_HROUU'],
						MB_Dime_Rate_RB_H: item['game']['IOR_HROUC'] == "" ? 0 : item['game']['IOR_HROUC'],
						TG_Dime_Rate_RB_H: item['game']['IOR_HROUH'] == "" ? 0 : item['game']['IOR_HROUH'],
						MB_Ball: item['game']['SCORE_H'],
						TG_Ball: item['game']['SCORE_C'],
						MB_Win_Rate_RB: item['game']['IOR_RMH'] == "" ? 0 : item['game']['IOR_RMH'],
						TG_Win_Rate_RB: item['game']['IOR_RMC'] == "" ? 0 : item['game']['IOR_RMC'],
						M_Flat_Rate_RB: item['game']['IOR_RMN'] == "" ? 0 : item['game']['IOR_RMN'],
						MB_Win_Rate_RB_H: item['game']['IOR_HRMH'] == "" ? 0 : item['game']['IOR_HRMH'],
						TG_Win_Rate_RB_H: item['game']['IOR_HRMC'] == "" ? 0 : item['game']['IOR_HRMC'],
						M_Flat_Rate_RB_H: item['game']['IOR_HRMN'] == "" ? 0 : item['game']['IOR_HRMN'],
						MB_Card: item['game']['REDCARD_H'] == "" ? 0 : item['game']['REDCARD_H'],
						TG_Card: item['game']['REDCARD_C'] == "" ? 0 : item['game']['REDCARD_C'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						RETIME_SET: item['game']["RETIMESET"].split("^")[0] == "MTIME" ? item['game']["RETIMESET"].split("^")[1] : item['game']["RETIMESET"].split("^")[0] + " " + item['game']["RETIMESET"].split("^")[1],
						// Eventid: item['game']['EVENTID'],
						Hot: item['game']['HOT'] == 'Y'? 1 : 0,
						Play: item['game']['PLAY'] == 'Y'? 1 : 0,
						MID: item['game']['GID'],
						HDP_OU: item['game']['OU_COUNT'] > 1 ? 1 : 0,
						CORNER: item['game']['CN_COUNT'] > 0 ? 1 : 0,
						RB_Show: 1,
						S_Show: 0,
						isSub: 1
					};
					itemList.push(data);						
				} else {
					let data = {
						showType: item['myGame'],
						Type: 'FT',
						ECID: item['game']['ECID'],
						LID: item['game']['LID'],
						MB_MID: item['game']['GNUM_H'],
						TG_MID: item['game']['GNUM_C'],
						S_Single_Rate: item['game']['IOR_EOO'],
						S_Double_Rate: item['game']['IOR_EOE'],
						Eventid: item['game']['EVENTID'],
						Hot: item['game']['HOT'],
						Play: item['game']['PLAY'],
						S_Show: 1,
						MB_Team: item['game']['TEAM_H'],
						TG_Team: item['game']['TEAM_C'],
						MB_Team_tw: item['game']['TEAM_H'],
						TG_Team_tw: item['game']['TEAM_C'],
						MB_Team_en: item['game']['TEAM_H'],
						TG_Team_en: item['game']['TEAM_C'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						M_League: item['game']['LEAGUE'],
						M_League_tw: item['game']['LEAGUE'],
						M_League_en: item['game']['LEAGUE'],
						M_Type: item['game']['RUNNING'] == "Y" ? 1 : 0,
						ShowTypeR: item['game']['STRONG'],
						MB_Win_Rate: item['game']['IOR_MH'],
						TG_Win_Rate: item['game']['IOR_MC'],
						M_Flat_Rate: item['game']['IOR_MN'],
						M_LetB: item['game']['RATIO_R'],
						MB_LetB_Rate: item['game']['IOR_RH'],
						TG_LetB_Rate: item['game']['IOR_RC'],
						MB_Dime: item['game']['RATIO_OUO'],
						TG_Dime: item['game']['RATIO_OUU'],
						MB_Dime_Rate: item['game']['IOR_OUC'],
						TG_Dime_Rate: item['game']['IOR_OUH'],
						ShowTypeHR: item['game']['HSTRONG'],
						MB_Win_Rate_H: item['game']['IOR_HMH'],
						TG_Win_Rate_H: item['game']['IOR_HMC'],
						M_Flat_Rate_H: item['game']['IOR_HMN'],
						M_LetB_H: item['game']['RATIO_HR'],
						MB_LetB_Rate_H: item['game']['IOR_HRH'],
						TG_LetB_Rate_H: item['game']['IOR_HRC'],
						MB_Dime_H: item['game']['RATIO_HOUO'],
						TG_Dime_H: item['game']['RATIO_HOUU'],
						MB_Dime_Rate_H: item['game']['IOR_HOUC'],
						TG_Dime_Rate_H: item['game']['IOR_HOUH'],
						ShowTypeRB: item['game']['STRONG'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						MID: item['game']['GID'],
						HDP_OU: item['game']['OU_COUNT'] > 1 ? 1 : 0,
						CORNER: item['game']['CN_COUNT'] > 0 ? 1 : 0,
					};
					itemList.push(data);						
				}
			}));
			
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
		await sleep(2000);
	}
}

exports.dispatchFT_MAIN_FAVORITE = (itemList) => {
	itemList.map(async item => {
		let response;
		try {
			if (item["showType"] === "rb") {
				response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_IN_PLAY}`, item);
			} else {
				response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_DEFAULT_TODAY}`, item);			
			}
			// console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err.response);
		}
	})
}

exports.getFT_LEAGUE_CHAMPION = async (thirdPartyAuthData) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_league_list_FS");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "FT");
		formData.append("FS", "Y");
		formData.append("showtype", "fu");
		formData.append("specialClick", "");
		formData.append("outrightsClick", "outrights");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		let area_arr = [];
		if (response.status === 200) {
			var result = parser.parse(response.data);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		// console.log(e)
		return null;
	}
}

exports.getFT_MAIN_CHAMPION = async (thirdPartyAuthData, data) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p", "get_game_list_FS");
		formData.append("gtype", "FT");
		formData.append("search", "all");
		formData.append("rtype", "fs");
		formData.append("league_id", data.lid);
		formData.append("date", "");
		formData.append("special", "");
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			return result['serverresponse']['game'];
		}
	} catch(e) {
		console.log(e)
	}
}

exports.dispatchFT_MAIN_CHAMPION = (itemList) => {
	var gid = 0;
	var datetime = "";
	var tt = [];
	var m_date = "";
	var m_time = "";
	var league = "";
	var teamsname = "";
	var gopen = "";
	var gamecount = "";
	var uptime = "";
	var rtypes = [];
	itemList.map(async item => {
		try {
			gid = item['gid'];
			datetime = item['datetime'];
			tt = datetime.split(" ");
			m_date = tt[0];
			m_time = tt[1];
			league = item['league'];
			teamsname = item['teamsname'];
			gopen = item['gopen'];
			gamecount = item['gamecount'];
			uptime = moment().format('Y-m-d H:i:s');
			rtypes = item['rtypes'];
			await Promise.all(rtypes.map(async item => {
				let result = item['result'];
				let rtype = item['rtype'];
				let teams = item['teams'];
				let ioratio = item['ioratio'];
				let data = {
					MID: gid,
					uptime: uptime,
					M_Start: datetime,
					MB_Team_tw: teams,
					M_League_tw: league,
					M_Item_tw: teamsname,
					MB_Team: teams,
					M_League: league,
					M_Item: teamsname,
					MB_Team_en: teams,
					M_League_en: league,
					M_Item_en: teamsname,
					// M_Area: m_area,
					M_Area: "",
					M_Rate: ioratio,
					Gid: rtype,
					mcount: gamecount,
					Gtype: 'FT',
					mshow: gopen,
					mshow2: result
				}
				response = await axios.post(`${BACKEND_BASE_URL}${MATCH_CROWN}/add`, data);
				// console.log("dispatchFT_MAIN_CHAMPION: ", response.data);
			}));
		} catch(e) {
			// console.log(e);
		}
	})
}

exports.getFT_FU_R_INPLAY = async (thirdPartyAuthData) => {
	try {
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		formData.append("date", "");
		formData.append("gtype", "ft");
		formData.append("showtype", "live");
		formData.append("rtype", "rb");
		formData.append("ltype", 3);
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("1111111111111111111111111111", result['serverresponse'])
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			await Promise.all(tempList.map(async item => {
				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);
				if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
				}
				// console.log("22222222222222", item['game']['IOR_HREH'] ?? 0)
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
					Type: 'FT',
					Retime: item['game']['TIMER'],
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],
					M_Date: m_date,
					M_Time: m_time,
					M_Start: m_start,
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					MB_Team_tw: item['game']['TEAM_H'],
					TG_Team_tw: item['game']['TEAM_C'],
					MB_Team_en: item['game']['TEAM_H'],
					TG_Team_en: item['game']['TEAM_C'],
					M_League: item['game']['LEAGUE'],
					M_League_tw: item['game']['LEAGUE'],
					M_League_en: item['game']['LEAGUE'],
					MB_MID: item['game']['GNUM_H'],
					TG_MID: item['game']['GNUM_C'],
					ShowTypeRB: item['game']['STRONG'] == "" ? "H" : item['game']['STRONG'],
					M_LetB_RB: item['game']['RATIO_RE'] == "" ? 0 : item['game']['RATIO_RE'],
					MB_LetB_Rate_RB: item['game']['IOR_REH'] == "" ? 0 : item['game']['IOR_REH'],
					TG_LetB_Rate_RB: item['game']['IOR_REC'] == "" ? 0 : item['game']['IOR_REC'],
					MB_Dime_RB: item['game']['RATIO_ROUO'] == "" ? 0 : item['game']['RATIO_ROUO'],
					TG_Dime_RB: item['game']['RATIO_ROUU'] == "" ? 0 : item['game']['RATIO_ROUU'],
					MB_Dime_Rate_RB: item['game']['IOR_ROUC'] == "" ? 0 : item['game']['IOR_ROUC'],
					TG_Dime_Rate_RB: item['game']['IOR_ROUH'] == "" ? 0 : item['game']['IOR_ROUH'],
					ShowTypeHRB: item['game']['HSTRONG'] == "" ? "H" : item['game']['HSTRONG'],
					M_LetB_RB_H: item['game']['RATIO_HRE'] == "" ? 0 : item['game']['RATIO_HRE'],
					MB_LetB_Rate_RB_H: item['game']['IOR_HREH'] == "" ? 0 : item['game']['IOR_HREH'],
					TG_LetB_Rate_RB_H: item['game']['IOR_HREC'] == "" ? 0 : item['game']['IOR_HREC'],
					MB_Dime_RB_H: item['game']['RATIO_HROUO'] == "" ? 0 : item['game']['RATIO_HROUO'],
					TG_Dime_RB_H: item['game']['RATIO_HROUU'] == "" ? 0 : item['game']['RATIO_HROUU'],
					MB_Dime_Rate_RB_H: item['game']['IOR_HROUC'] == "" ? 0 : item['game']['IOR_HROUC'],
					TG_Dime_Rate_RB_H: item['game']['IOR_HROUH'] == "" ? 0 : item['game']['IOR_HROUH'],
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					MB_Win_Rate_RB: item['game']['IOR_RMH'] == "" ? 0 : item['game']['IOR_RMH'],
					TG_Win_Rate_RB: item['game']['IOR_RMC'] == "" ? 0 : item['game']['IOR_RMC'],
					M_Flat_Rate_RB: item['game']['IOR_RMN'] == "" ? 0 : item['game']['IOR_RMN'],
					MB_Win_Rate_RB_H: item['game']['IOR_HRMH'] == "" ? 0 : item['game']['IOR_HRMH'],
					TG_Win_Rate_RB_H: item['game']['IOR_HRMC'] == "" ? 0 : item['game']['IOR_HRMC'],
					M_Flat_Rate_RB_H: item['game']['IOR_HRMN'] == "" ? 0 : item['game']['IOR_HRMN'],
					MB_Card: item['game']['REDCARD_H'] == "" ? 0 : item['game']['REDCARD_H'],
					TG_Card: item['game']['REDCARD_C'] == "" ? 0 : item['game']['REDCARD_C'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					RETIME_SET: item['game']["RETIMESET"].split("^")[0] == "MTIME" ? item['game']["RETIMESET"].split("^")[1] : item['game']["RETIMESET"].split("^")[0] + " " + item['game']["RETIMESET"].split("^")[1],
					// Eventid: item['game']['EVENTID'],
					Hot: item['game']['HOT'] == 'Y'? 1 : 0,
					Play: item['game']['PLAY'] == 'Y'? 1 : 0,
					MID: item['game']['GID'],
					HDP_OU: item['game']['OU_COUNT'] > 1 ? 1 : 0,
					CORNER: item['game']['CN_COUNT'] > 0 ? 1 : 0,
					RB_Show: 1,
					S_Show: 0,
					isSub: 1
				};
				itemList.push(data);
			}));
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
		await sleep(2000);
	}
}

exports.dispatchFT_FU_R_INPLAY = (itemList) => {
	itemList.map(async item => {
		try {
			// if (item["MID"] == 5997873) {
			// 	console.log("5997873: ", item);
			// }
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_IN_PLAY}`, item);
			// await sleep(2000);
			console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err.response);
		}
	})
}

exports.getFT_HDP_OU_INPLAY = async (thirdPartyAuthData, item) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_game_OBT");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "ft");
		formData.append("showtype", "live");
		formData.append("isSpecial", "");
		formData.append("isEarly", "N");
		formData.append("model", "ROU|MIX");
		formData.append("isETWI", "N");
		formData.append("ecid", item["ecid"]);
		formData.append("ltype", 3);
		formData.append("is_rb", "Y");
		formData.append("ts", new Date().getTime());

		response = await axios.post(thirdPartyUrl, formData);

		let data = {};
		data["MID"] = item["id"];
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("getFT_HDP_OU_INPLAY:=============", result);
			if (result["serverresponse"]["code"] != "noData" && result["serverresponse"]["ec"]["game"].length > 0) {
				for (let i = 0; i < result["serverresponse"]["ec"]["game"].length; i++) {
					// if (i == 0) {
					// 	data["RATIO_RE_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
					// 	data["IOR_REH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
					// 	data["IOR_REC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
					// 	data["RATIO_ROUO_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
					// 	data["RATIO_ROUU_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
					// 	data["IOR_ROUH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
					// 	data["IOR_ROUC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					// }
					if (i == 1) {
						data["M_LetB_RB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
						data["MB_LetB_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
						data["TG_LetB_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
						data["MB_Dime_RB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
						data["TG_Dime_RB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
						data["MB_Dime_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
						data["TG_Dime_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					}
					if (i == 2) {
						data["M_LetB_RB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
						data["MB_LetB_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
						data["TG_LetB_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
						data["MB_Dime_RB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
						data["TG_Dime_RB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
						data["MB_Dime_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
						data["TG_Dime_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					}
					if (i == 3) {
						data["M_LetB_RB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
						data["MB_LetB_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
						data["TG_LetB_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
						data["MB_Dime_RB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
						data["TG_Dime_RB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
						data["MB_Dime_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
						data["TG_Dime_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					}
				}
				return data;
			}
			return data;
		}
		return data;
	} catch(e) {
		// console.log(e)
	}
}

exports.dispatchFT_HDP_OU_INPLAY = async (item) => {
	try {									
		response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_HDP_OBT}`, item);
		// console.log("FT_HDP_OBT_RESPONSE:===============", response.data);
	} catch(err) {
		console.log("FT_HDP_OBT_ERROR:===============", err);
	}
}

exports.getFT_CORNER_INPLAY = async (thirdPartyAuthData, item) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		formData = new FormData();
		formData.append("p", "get_game_OBT");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "ft");
		formData.append("showtype", "live");
		formData.append("isSpecial", "");
		formData.append("isEarly", "N");
		formData.append("model", "CN");
		formData.append("isETWI", "N");
		formData.append("ecid", item["ecid"]);
		formData.append("ltype", 3);
		formData.append("is_rb", "Y");
		formData.append("ts", new Date().getTime());
		formData.append("isClick", "Y");

		let cnResponse = await axios.post(thirdPartyUrl, formData);

		let data = {};

		if (cnResponse.status === 200) {

			let result = parser.parse(cnResponse.data);

			if (result["serverresponse"]["code"] !== "noData") {

				// console.log("getFT_CORNER_INPLAY:===============", result["serverresponse"]["ec"]["game"]);

				let item = result["serverresponse"]["ec"];

				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);
				if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;

				data = {
					Type: 'FT',
					Retime: item['game']['TIMER'],
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],
					MID: item['game']['GID'],
					M_Date: m_date,
					M_Time: m_time,
					M_Start: m_start,
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					MB_Team_tw: item['game']['TEAM_H'],
					TG_Team_tw: item['game']['TEAM_C'],
					MB_Team_en: item['game']['TEAM_H'],
					TG_Team_en: item['game']['TEAM_C'],
					M_League: item['game']['LEAGUE'],
					M_League_tw: item['game']['LEAGUE'],
					M_League_en: item['game']['LEAGUE'],
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					MB_MID: item['game']['GNUM_H'],
					TG_MID: item['game']['GNUM_C'],
					ShowTypeRB: item['game']['STRONG'] == "" ? "H" : item['game']['STRONG'],
					MB_Dime_RB: item['game']['RATIO_ROUO'] == "" ? "" : item['game']['RATIO_ROUO'],
					TG_Dime_RB: item['game']['RATIO_ROUU'] == "" ? "" : item['game']['RATIO_ROUU'],
					MB_Dime_Rate_RB: item['game']['IOR_ROUC'] == "" ? 0 : item['game']['IOR_ROUC'],
					TG_Dime_Rate_RB: item['game']['IOR_ROUH'] == "" ? 0 : item['game']['IOR_ROUH'],
					ShowTypeHRB: item['game']['HSTRONG'] == "" ? "H" : item['game']['HSTRONG'],
					MB_Dime_RB_H: item['game']['RATIO_HROUO'] == "" ? "" : item['game']['RATIO_HROUO'],
					TG_Dime_RB_H: item['game']['RATIO_HROUU'] == "" ? "" : item['game']['RATIO_HROUU'],
					MB_Dime_Rate_RB_H: item['game']['IOR_HROUC'] == "" ? 0 : item['game']['IOR_HROUC'],
					TG_Dime_Rate_RB_H: item['game']['IOR_HROUH'] == "" ? 0 : item['game']['IOR_HROUH'],
					S_Single_Rate: item['game']["IOR_REOO"] == "" ? 0 : item['game']["IOR_REOO"],
					S_Double_Rate: item['game']["IOR_REOE"] == "" ? 0 : item["game"]["IOR_REOE"],
					S_Single_Rate_H: item['game']["IOR_HREOO"] == "" ? 0 : item['game']["IOR_HREOO"],
					S_Double_Rate_H: item['game']["IOR_HREOE"] == "" ? 0 : item["game"]["IOR_HREOE"],
					RB_Show: 1,
					S_Show: 0,
					isSub: 1
				};
			}
			return data;
		}
		return data;
	} catch(e) {
		// console.log(e)
	}
}

exports.dispatchFT_CORNER_INPLAY = async (item) => {
	try {									
		response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_CORNER_OBT}`, item);
		// console.log("FT_CORNER_OBT_RESPONSE:===============", response.data);
	} catch(err) {
		console.log("FT_CORNER_OBT_ERROR:===============", err);
	}
}

exports.getFT_CORRECT_SCORE_INPLAY = async (thirdPartyAuthData) => {
	try {
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		formData.append("date", "");
		formData.append("gtype", "ft");
		formData.append("showtype", "live");
		formData.append("rtype", "rpd");
		formData.append("ltype", 3);
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("correctScore:====================", result);
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount === 0) return null;
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			tempList.map(async item => {
				let data = {
					Type: "FT",
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],			
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					M_League: item['game']['LEAGUE'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					RETIME_SET: item['game']["RETIMESET"].split("^")[0] == "MTIME" ? item['game']["RETIMESET"].split("^")[1] : item['game']["RETIMESET"].split("^")[0] + " " + item['game']["RETIMESET"].split("^")[1],
					MB1TG0: item['game']['IOR_RH1C0'],
					MB2TG0: item['game']['IOR_RH2C0'],
					MB2TG1: item['game']['IOR_RH2C1'],
					MB3TG0: item['game']['IOR_RH3C0'],
					MB3TG1: item['game']['IOR_RH3C1'],
					MB3TG2: item['game']['IOR_RH3C2'],
					MB4TG0: item['game']['IOR_RH4C0'],
					MB4TG1: item['game']['IOR_RH4C1'],
					MB4TG2: item['game']['IOR_RH4C2'],
					MB4TG3: item['game']['IOR_RH4C3'],
					MB0TG0: item['game']['IOR_RH0C0'],
					MB1TG1: item['game']['IOR_RH1C1'],
					MB2TG2: item['game']['IOR_RH2C2'],
					MB3TG3: item['game']['IOR_RH3C3'],
					MB4TG4: item['game']['IOR_RH4C4'],
					MB0TG1: item['game']['IOR_RH0C1'],
					MB0TG2: item['game']['IOR_RH0C2'],
					MB1TG2: item['game']['IOR_RH1C2'],
					MB0TG3: item['game']['IOR_RH0C3'],
					MB1TG3: item['game']['IOR_RH1C3'],
					MB2TG3: item['game']['IOR_RH2C3'],
					MB0TG4: item['game']['IOR_RH0C4'],
					MB1TG4: item['game']['IOR_RH1C4'],
					MB2TG4: item['game']['IOR_RH2C4'],
					MB3TG4: item['game']['IOR_RH3C4'],
					UP5: item['game']['IOR_ROVH'],
					MID: item['game']['GID'],
					PD_Show: 1,
				};
				itemList.push(data);
			})
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.dispatchFT_CORRECT_SCORE_INPLAY = (itemList) => {
	itemList.map(async item => {		
		try {
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_CORRECT_SCORE}`, item);
			// console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err);
		}
	})
}

exports.getFT_LEAGUE_TODAY = async (thirdPartyAuthData) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		console.log(thirdPartyAuthData);

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_league_list_All");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "FT");
		formData.append("FS", "N");
		formData.append("showtype", "ft");
		formData.append("date", 0);
		formData.append("ts", new Date().getTime());
		formData.append("nocp", "N");

		response = await axios.post(thirdPartyUrl, formData);

		if (response.status === 200) {
			var result = parser.parse(response.data);
			// console.log("getFT_LEAGUE_TODAY:=============", result);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		console.log(e)
		return null;
	}
}

exports.getFT_LEAGUE_EARLY = async (thirdPartyAuthData) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_league_list_All");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "FT");
		formData.append("FS", "N");
		formData.append("showtype", "fu");
		formData.append("date", "all");
		formData.append("ts", new Date().getTime());
		formData.append("nocp", "N");

		response = await axios.post(thirdPartyUrl, formData);

		if (response.status === 200) {
			var result = parser.parse(response.data);
			// console.log("getFT_LEAGUE_EARLY:=============", result["serverresponse"]);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		// console.log(e)
		return null;
	}
}

exports.getFT_LEAGUE_PARLAY = async (thirdPartyAuthData) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_league_list_All");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "FT");
		formData.append("FS", "N");
		formData.append("showtype", "p3");
		formData.append("date", "all");
		formData.append("ts", new Date().getTime());
		formData.append("nocp", "N");

		response = await axios.post(thirdPartyUrl, formData);

		if (response.status === 200) {
			var result = parser.parse(response.data);
			// console.log("getFT_LEAGUE_TODAY:=============", result["serverresponse"]);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		// console.log(e)
		return null;
	}
}

exports.getFT_DEFAULT_TODAY = async (thirdPartyAuthData, data) => {
	try {
		// console.log(data);
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		formData.append("date", 0);
		formData.append("gtype", "ft");
		formData.append("showtype", "today");
		formData.append("rtype", "r");
		formData.append("ltype", 3);
		formData.append("lid", data.lids);
		if (data.field != "") {
			formData.append("field", data.field);
			formData.append("action", "clickCoupon");
		} else {
			formData.append("action", "click_league");			
		}
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("getFT_DEFAULT_TODAY:=========", result['serverresponse'])
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			await Promise.all(tempList.map(async item => {
					let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
					let m_time = item['game']['DATETIME'].split(" ")[1];
					let time = m_time.split(":")[0];
					let temp_minute = m_time.split(":")[1];
					let minute = temp_minute.substring(0, temp_minute.length - 1);
					var lastChar = temp_minute.substr(temp_minute.length - 1);
					if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
					}
					let m_start = m_date + " " + time + ":" + minute;
					m_time = time + ":" + minute;
					let data = {
						Type: 'FT',
						ECID: item['game']['ECID'],
						LID: item['game']['LID'],
						MB_MID: item['game']['GNUM_H'],
						TG_MID: item['game']['GNUM_C'],
						S_Single_Rate: item['game']['IOR_EOO'],
						S_Double_Rate: item['game']['IOR_EOE'],
						Eventid: item['game']['EVENTID'],
						Hot: item['game']['HOT'],
						Play: item['game']['PLAY'],
						S_Show: 1,
						MB_Team: item['game']['TEAM_H'],
						TG_Team: item['game']['TEAM_C'],
						MB_Team_tw: item['game']['TEAM_H'],
						TG_Team_tw: item['game']['TEAM_C'],
						MB_Team_en: item['game']['TEAM_H'],
						TG_Team_en: item['game']['TEAM_C'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						M_League: item['game']['LEAGUE'],
						M_League_tw: item['game']['LEAGUE'],
						M_League_en: item['game']['LEAGUE'],
						M_Type: item['game']['RUNNING'] == "Y" ? 1 : 0,
						ShowTypeR: item['game']['STRONG'],
						MB_Win_Rate: item['game']['IOR_MH'],
						TG_Win_Rate: item['game']['IOR_MC'],
						M_Flat_Rate: item['game']['IOR_MN'],
						M_LetB: item['game']['RATIO_R'],
						MB_LetB_Rate: item['game']['IOR_RH'],
						TG_LetB_Rate: item['game']['IOR_RC'],
						MB_Dime: item['game']['RATIO_OUO'],
						TG_Dime: item['game']['RATIO_OUU'],
						MB_Dime_Rate: item['game']['IOR_OUC'],
						TG_Dime_Rate: item['game']['IOR_OUH'],
						ShowTypeHR: item['game']['HSTRONG'],
						MB_Win_Rate_H: item['game']['IOR_HMH'],
						TG_Win_Rate_H: item['game']['IOR_HMC'],
						M_Flat_Rate_H: item['game']['IOR_HMN'],
						M_LetB_H: item['game']['RATIO_HR'],
						MB_LetB_Rate_H: item['game']['IOR_HRH'],
						TG_LetB_Rate_H: item['game']['IOR_HRC'],
						MB_Dime_H: item['game']['RATIO_HOUO'],
						TG_Dime_H: item['game']['RATIO_HOUU'],
						MB_Dime_Rate_H: item['game']['IOR_HOUC'],
						TG_Dime_Rate_H: item['game']['IOR_HOUH'],
						ShowTypeRB: item['game']['STRONG'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						MID: item['game']['GID'],
						HDP_OU: item['game']['OU_COUNT'] > 1 ? 1 : 0,
						CORNER: item['game']['CN_COUNT'] > 0 ? 1 : 0,
					};
					itemList.push(data);
				}));
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.getFT_MORE_TODAY = async (thirdPartyAuthData, item) => {

	try {

		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_game_more");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "ft");
		formData.append("showtype", item["showtype"]);
		formData.append("specialClick", "");
		formData.append("mode", "");
		formData.append("filter", "Main");
		formData.append("lid", item["lid"]);
		formData.append("ecid", item["ecid"]);
		formData.append("ltype", 3);
		formData.append("isRB", "N");
		formData.append("ts", new Date().getTime());

		response = await axios.post(thirdPartyUrl, formData);

		// console.log(response);

		let data = {};

		if (response.status === 200) {
			let result = parser.parse(response.data);

			if (result["serverresponse"]["code"] == "noData") return data;

			let item = {};

			if (Array.isArray(result["serverresponse"]["game"])) {

				item = result["serverresponse"]["game"][0];

			} else {

				item = result["serverresponse"]["game"];
			}


			//半全场
			if( item['sw_F'] == 'Y' ) {
				data["MBMB"] = item["ior_FHH"];
				data["MBFT"] = item["ior_FHN"];
				data["MBTG"] = item["ior_FHC"];
				data["FTMB"] = item["ior_FNH"];
				data["FTFT"] = item["ior_FNN"];
				data["FTTG"] = item["ior_FNC"];
				data["TGMB"] = item["ior_FCH"];
				data["TGTG"] = item["ior_FCC"];
				data["TGFT"] = item["ior_FCN"];
				data["F_Show"] = 1;
			}

			//总入球数

			if( item['sw_T'] == 'Y' ) {
				data["S_0_1"] = item["ior_T01"];
				data["S_2_3"] = item["ior_T23"];
				data["S_4_6"] = item["ior_T46"];
				data["S_7UP"] = item["ior_OVER"];
				data["T_Show"] = 1;
			}

			//上半波胆

			if(item['sw_HPD']=='Y'){
				data["MB1TG0H"] = item["ior_HH1C0"];
				data["MB2TG0H"] = item["ior_HH2C0"];
				data["MB2TG1H"] = item["ior_HH2C1"];
				data["MB3TG0H"] = item["ior_HH3C0"];
				data["MB3TG1H"] = item["ior_HH3C1"];
				data["MB3TG2H"] = item["ior_HH3C2"];
				data["MB4TG0H"] = item["ior_HH4C0"];
				data["MB4TG1H"] = item["ior_HH4C1"];
				data["MB4TG2H"] = item["ior_HH4C2"];
				data["MB4TG3H"] = item["ior_HH4C3"];
				data["MB0TG0H"] = item["ior_HH0C0"];
				data["MB1TG1H"] = item["ior_HH1C1"];
				data["MB2TG2H"] = item["ior_HH2C2"];
				data["MB3TG3H"] = item["ior_HH3C3"];
				data["MB4TG4H"] = item["ior_HH4C4"];
				data["MB0TG1H"] = item["ior_HH0C1"];
				data["MB0TG2H"] = item["ior_HH0C2"];
				data["MB1TG2H"] = item["ior_HH1C2"];
				data["MB0TG3H"] = item["ior_HH0C3"];
				data["MB1TG3H"] = item["ior_HH1C3"];
				data["MB2TG3H"] = item["ior_HH2C3"];
				data["MB0TG4H"] = item["ior_HH0C4"];
				data["MB1TG4H"] = item["ior_HH1C4"];
				data["MB2TG4H"] = item["ior_HH2C4"];
				data["MB3TG4H"] = item["ior_HH3C4"];
				data["UP5H"] = item["ior_HOVH"];
				data["HPD_Show"] = 1;
		    }


			return data;
		}

		return data;

	} catch(e) {
		// console.log(e)
	}
}


exports.getFT_DEFAULT_EARLY = async (thirdPartyAuthData, data) => {
	try {
		// console.log(data);
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		if (data.field === "cpl") {			
			formData.append("date", 1);
		} else {
			formData.append("date", "all");
		}
		formData.append("gtype", "ft");
		formData.append("showtype", "early");
		formData.append("rtype", "r");
		formData.append("ltype", 3);
		formData.append("lid", data.lids);
		if (data.field != "") {
			formData.append("field", data.field);
			formData.append("action", "clickCoupon");
		} else {
			formData.append("action", "click_league");			
		}
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("getFT_DEFAULT_TODAY:=========", result['serverresponse'])
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			await Promise.all(tempList.map(async item => {
					let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
					let m_time = item['game']['DATETIME'].split(" ")[1];
					let time = m_time.split(":")[0];
					let temp_minute = m_time.split(":")[1];
					let minute = temp_minute.substring(0, temp_minute.length - 1);
					var lastChar = temp_minute.substr(temp_minute.length - 1);
					if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
					}
					let m_start = m_date + " " + time + ":" + minute;
					m_time = time + ":" + minute;
					let data = {
						Type: 'FT',
						ECID: item['game']['ECID'],
						LID: item['game']['LID'],
						MB_MID: item['game']['GNUM_H'],
						TG_MID: item['game']['GNUM_C'],
						S_Single_Rate: item['game']['IOR_EOO'],
						S_Double_Rate: item['game']['IOR_EOE'],
						Eventid: item['game']['EVENTID'],
						Hot: item['game']['HOT'],
						Play: item['game']['PLAY'],
						S_Show: 1,
						MB_Team: item['game']['TEAM_H'],
						TG_Team: item['game']['TEAM_C'],
						MB_Team_tw: item['game']['TEAM_H'],
						TG_Team_tw: item['game']['TEAM_C'],
						MB_Team_en: item['game']['TEAM_H'],
						TG_Team_en: item['game']['TEAM_C'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						M_League: item['game']['LEAGUE'],
						M_League_tw: item['game']['LEAGUE'],
						M_League_en: item['game']['LEAGUE'],
						M_Type: item['game']['RUNNING'] == "Y" ? 1 : 0,
						ShowTypeR: item['game']['STRONG'],
						MB_Win_Rate: item['game']['IOR_MH'],
						TG_Win_Rate: item['game']['IOR_MC'],
						M_Flat_Rate: item['game']['IOR_MN'],
						M_LetB: item['game']['RATIO_R'],
						MB_LetB_Rate: item['game']['IOR_RH'],
						TG_LetB_Rate: item['game']['IOR_RC'],
						MB_Dime: item['game']['RATIO_OUO'],
						TG_Dime: item['game']['RATIO_OUU'],
						MB_Dime_Rate: item['game']['IOR_OUC'],
						TG_Dime_Rate: item['game']['IOR_OUH'],
						ShowTypeHR: item['game']['HSTRONG'],
						MB_Win_Rate_H: item['game']['IOR_HMH'],
						TG_Win_Rate_H: item['game']['IOR_HMC'],
						M_Flat_Rate_H: item['game']['IOR_HMN'],
						M_LetB_H: item['game']['RATIO_HR'],
						MB_LetB_Rate_H: item['game']['IOR_HRH'],
						TG_LetB_Rate_H: item['game']['IOR_HRC'],
						MB_Dime_H: item['game']['RATIO_HOUO'],
						TG_Dime_H: item['game']['RATIO_HOUU'],
						MB_Dime_Rate_H: item['game']['IOR_HOUC'],
						TG_Dime_Rate_H: item['game']['IOR_HOUH'],
						ShowTypeRB: item['game']['STRONG'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						MID: item['game']['GID'],
						HDP_OU: item['game']['OU_COUNT'] > 1 ? 1 : 0,
						CORNER: item['game']['CN_COUNT'] > 0 ? 1 : 0,
					};
					itemList.push(data);
				}));
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.getFT_DEFAULT_PARLAY = async (thirdPartyAuthData, data) => {
	try {
		// console.log(data);
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		if (data.field === "cp1") {
			formData.append("p3type", "RP3");
			formData.append("date", "all");
		}else if (data.field === "cp2") {
			formData.append("p3type", "P3");
			formData.append("date", 0);
		} else if (data.field === "HotGame_FT_lid_1" || data.field === "HotGame_FT_lid_2") {
			formData.append("p3type", "ALL");
			formData.append("date", "all");
		} else {
			formData.append("p3type", "");
			formData.append("date", "all");
		}
		formData.append("gtype", "ft");
		formData.append("showtype", "parlay");
		formData.append("rtype", "rb");
		formData.append("ltype", 3);
		formData.append("lid", data.lids);
		if (data.field != "") {
			formData.append("field", data.field);
			formData.append("action", "clickCoupon");
		} else {
			formData.append("action", "click_league");			
		}
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log(result);
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			await Promise.all(tempList.map(async item => {
				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);
				if (lastChar == "p") {
					if (Number(time) !== 12) {
						time = Number(time) + 12;
					}
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
					Type: 'FT',
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],
					MB_MID: item['game']['GNUM_H'],
					TG_MID: item['game']['GNUM_C'],
					S_P_Single_Rate: item['game']['IOR_REOO'],
					S_P_Double_Rate: item['game']['IOR_REOE'],
					Eventid: item['game']['EVENTID'],
					Hot: item['game']['HOT'],
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					MB_Team_tw: item['game']['TEAM_H'],
					TG_Team_tw: item['game']['TEAM_C'],
					MB_Team_en: item['game']['TEAM_H'],
					TG_Team_en: item['game']['TEAM_C'],
					M_Date: m_date,
					M_Time: m_time,
					M_Start: m_start,
					M_League: item['game']['LEAGUE'],
					M_League_tw: item['game']['LEAGUE'],
					M_League_en: item['game']['LEAGUE'],
					M_Type: item['game']['RUNNING'] == "Y" ? 1 : 0,
					MB_P_Win_Rate: item['game']['IOR_RMH'],
					TG_P_Win_Rate: item['game']['IOR_RMC'],
					M_P_Flat_Rate: item['game']['IOR_RMN'],
					M_P_LetB: item['game']['RATIO_RE'],
					MB_P_LetB_Rate: item['game']['IOR_REH'],
					TG_P_LetB_Rate: item['game']['IOR_REC'],
					MB_P_Dime: item['game']['RATIO_ROUO'],
					TG_P_Dime: item['game']['RATIO_ROUU'],
					MB_P_Dime_Rate: item['game']['IOR_ROUC'],
					TG_P_Dime_Rate: item['game']['IOR_ROUH'],
					MB_P_Win_Rate_H: item['game']['IOR_HRMH'],
					TG_P_Win_Rate_H: item['game']['IOR_HRMC'],
					M_P_Flat_Rate_H: item['game']['IOR_HRMN'],
					M_P_LetB_H: item['game']['RATIO_HRE'],
					MB_P_LetB_Rate_H: item['game']['IOR_HREH'],
					TG_P_LetB_Rate_H: item['game']['IOR_HREC'],
					MB_P_Dime_H: item['game']['RATIO_HROUO'],
					TG_P_Dime_H: item['game']['RATIO_HROUU'],
					MB_P_Dime_Rate_H: item['game']['IOR_HROUC'],
					TG_P_Dime_Rate_H: item['game']['IOR_HROUH'],
					ShowTypeP: item['game']['STRONG'],
					ShowTypeHP: item['game']['HSTRONG'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					MB_Ball: item['game']['SCORE_H'] == undefined ? 0 : item["game"]['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'] == undefined ? 0 : item["game"]['SCORE_C'],
					P3_Show: 1,
					MID: item['game']['GID'],
					HDP_OU: item['game']['OU_COUNT'] > 1 ? 1 : 0,
					CORNER: item['game']['CN_COUNT'] > 0 ? 1 : 0,
				};
				itemList.push(data);
			}));
			return itemList;
		}
		return itemList;
	} catch(e) {
		console.log(e)
	}
}

exports.dispatchFT_DEFAULT_TODAY = (itemList) => {
	itemList.map(async item => {	
		try {
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_DEFAULT_TODAY}`, item);
			// console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err);
		}
	})
}

exports.dispatchFT_DEFAULT_PARLAY = (itemList) => {
	itemList.map(async item => {	
		try {
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_DEFAULT_PARLAY}`, item);
			console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err);
		}
	})
}

exports.getFT_HDP_OU_TODAY = async (thirdPartyAuthData, item) => {
	try {
		// console.log(item);
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_game_OBT");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "ft");
		formData.append("showtype", item["showtype"]);
		formData.append("isSpecial", "");
		formData.append("isEarly", "N");
		formData.append("model", "OU|MIX");
		formData.append("isETWI", "N");
		formData.append("ecid", item["ecid"]);
		formData.append("ltype", 3);
		formData.append("is_rb", "N");
		formData.append("ts", new Date().getTime());
		formData.append("isClick", "Y");

		response = await axios.post(thirdPartyUrl, formData);

		let data = {};
		data["MID"] = item["id"];
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("getGameOBT:=============", result);
			if (result["serverresponse"]["code"] != "noData" && result["serverresponse"]["ec"]["game"].length > 0) {
				for (let i = 0; i < result["serverresponse"]["ec"]["game"].length; i++) {

					// if (i == 0) {
					// 	data["RATIO_RE_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_R"];
					// 	data["IOR_REH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_RH"];
					// 	data["IOR_REC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_RC"];
					// 	data["RATIO_ROUO_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUO"];
					// 	data["RATIO_ROUU_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUU"];
					// 	data["IOR_ROUH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUH"];
					// 	data["IOR_ROUC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUC"];
					// }

					if (i == 1) {
						data["M_LetB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_R"];
						data["MB_LetB_Rate_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_RH"];
						data["TG_LetB_Rate_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_RC"];
						data["MB_Dime_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUO"];
						data["TG_Dime_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUU"];
						data["MB_Dime_Rate_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUH"];
						data["TG_Dime_Rate_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUC"];
					}
					if (i == 2) {
						data["M_LetB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_R"];
						data["MB_LetB_Rate_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_RH"];
						data["TG_LetB_Rate_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_RC"];
						data["MB_Dime_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUO"];
						data["TG_Dime_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUU"];
						data["MB_Dime_Rate_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUH"];
						data["TG_Dime_Rate_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUC"];
					}
					if (i == 3) {
						data["M_LetB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_R"];
						data["MB_LetB_Rate_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_RH"];
						data["TG_LetB_Rate_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_RC"];
						data["MB_Dime_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUO"];
						data["TG_Dime_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_OUU"];
						data["MB_Dime_Rate_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUH"];
						data["TG_Dime_Rate_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_OUC"];
					}
				}
				return data;
			}
			return data;
		}
		return data;
	} catch(e) {
		// console.log(e)
		await sleep(2000);
	}
}

exports.getFT_HDP_OU_PARLAY = async (thirdPartyAuthData, item) => {
	try {
		// console.log(item);
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		let formData = new FormData();
		formData.append("p", "get_game_OBT");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "ft");
		formData.append("showtype", item["showtype"]);
		formData.append("isSpecial", "");
		formData.append("isEarly", "N");
		formData.append("model", "ROU|MIX");
		formData.append("isETWI", "N");
		formData.append("ecid", item["ecid"]);
		formData.append("ltype", 3);
		if (item.field === "cp1") {
			formData.append("is_rb", "Y");			
		} else {
			formData.append("is_rb", "N");			
		}
		formData.append("ts", new Date().getTime());
		formData.append("isClick", "Y");

		response = await axios.post(thirdPartyUrl, formData);

		let data = {};
		data["MID"] = item["id"];
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("getGameOBT:=============", result);
			if (result["serverresponse"]["code"] != "noData" && result["serverresponse"]["ec"]["game"].length > 0) {
				for (let i = 0; i < result["serverresponse"]["ec"]["game"].length; i++) {					
					if (i == 1) {
						data["M_P_LetB_RB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
						data["MB_P_LetB_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
						data["TG_P_LetB_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
						data["MB_P_Dime_RB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
						data["TG_P_Dime_RB_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
						data["MB_P_Dime_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
						data["TG_P_Dime_Rate_RB_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					}
					if (i == 2) {
						data["M_P_LetB_RB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
						data["MB_P_LetB_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
						data["TG_P_LetB_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
						data["MB_P_Dime_RB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
						data["TG_P_Dime_RB_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
						data["MB_P_Dime_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
						data["TG_P_Dime_Rate_RB_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					}
					if (i == 3) {
						data["M_P_LetB_RB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
						data["MB_P_LetB_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
						data["TG_P_LetB_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
						data["MB_P_Dime_RB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
						data["TG_P_Dime_RB_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
						data["MB_P_Dime_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
						data["TG_P_Dime_Rate_RB_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					}
				}
				return data;
			}
			return data;
		}
	} catch(e) {
		// console.log(e)
		await sleep(2000);
	}
}

exports.getFT_CORNER_TODAY = async (thirdPartyAuthData, item) => {
	try {
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];

		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

		formData = new FormData();
		formData.append("p", "get_game_OBT");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("gtype", "ft");
		formData.append("showtype", "today");
		formData.append("isSpecial", "");
		formData.append("isEarly", "N");
		formData.append("model", "CN");
		formData.append("isETWI", "N");
		formData.append("ecid", item["ecid"]);
		formData.append("ltype", 3);
		formData.append("is_rb", "N");
		formData.append("ts", new Date().getTime());
		formData.append("isClick", "Y");

		let cnResponse = await axios.post(thirdPartyUrl, formData);

		let data = {};

		if (cnResponse.status === 200) {

			let result = parser.parse(cnResponse.data);

			if (result["serverresponse"]["code"] !== "noData") {

				let item = result["serverresponse"]["ec"];

				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);

				if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
				}

				let m_start = m_date + " " + time + ":" + minute;

				m_time = time + ":" + minute;

				data = {
					Type: 'FT',
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],
					MID: item['game']['GID'],
					MB_MID: item['game']['GNUM_H'],
					TG_MID: item['game']['GNUM_C'],
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					MB_Team_tw: item['game']['TEAM_H'],
					TG_Team_tw: item['game']['TEAM_C'],
					MB_Team_en: item['game']['TEAM_H'],
					TG_Team_en: item['game']['TEAM_C'],
					M_Date: m_date,
					M_Time: m_time,
					M_Start: m_start,
					M_Type: 0,
					M_League: item['game']['LEAGUE'],
					M_League_tw: item['game']['LEAGUE'],
					M_League_en: item['game']['LEAGUE'],
					ShowTypeR: item['game']['STRONG'],
					ShowTypeHR: item['game']['HSTRONG'],
					MB_Win_Rate: item['game']['IOR_MH'],
					TG_Win_Rate: item['game']['IOR_MC'],
					M_Flat_Rate: item['game']['IOR_MN'],
					MB_Win_Rate_H: item['game']['IOR_HMH'],
					TG_Win_Rate_H: item['game']['IOR_HMC'],
					M_Flat_Rate_H: item['game']['IOR_HMN'],
					M_LetB: item['game']['RATIO_R'],
					MB_LetB_Rate: item['game']['IOR_RH'],
					TG_LetB_Rate: item['game']['IOR_RC'],
					M_LetB_H: item['game']['RATIO_HR'],
					MB_LetB_Rate_H: item['game']['IOR_HRH'],
					TG_LetB_Rate_H: item['game']['IOR_HRC'],
					MB_Dime: item['game']['RATIO_OUO'],
					TG_Dime: item['game']['RATIO_OUU'],
					MB_Dime_Rate: item['game']['IOR_OUC'],
					TG_Dime_Rate: item['game']['IOR_OUH'],
					MB_Dime_H: item['game']['RATIO_HOUO'],
					TG_Dime_H: item['game']['RATIO_HOUU'],
					MB_Dime_Rate_H: item['game']['IOR_HOUC'],
					TG_Dime_Rate_H: item['game']['IOR_HOUH'],
					S_Show: 1,
				};
			}
			return data;
		}
		return data;
	} catch(e) {
		// console.log(e)
	}
}

exports.dispatchFT_CORNER_TODAY = async (item) => {
	try {									
		response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_CORNER_TODAY}`, item);
		// console.log("FT_CORNER_OBT_RESPONSE:===============", response.data);
	} catch(err) {
		console.log("FT_CORNER_OBT_ERROR:===============", err);
	}
}

exports.getFT_CORRECT_SCORE_TODAY = async (thirdPartyAuthData, data) => {
	try {
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		formData.append("date", 0);
		formData.append("gtype", "ft");
		formData.append("showtype", "today");
		formData.append("rtype", "pd");
		formData.append("ltype", 3);
		formData.append("lid", data.lids);
		if (data.field != "") {
			formData.append("field", data.field);
			formData.append("action", "clickCoupon");
		} else {
			formData.append("action", "click_league");			
		}
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("correctScore:====================", result);
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			tempList.map(async item => {
				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);
				if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
					Type: "FT",
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],			
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					M_League: item['game']['LEAGUE'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					M_Date: m_date,
					M_Time: m_time,
					M_Start: m_start,
					MB1TG0: item['game']['IOR_H1C0'],
					MB2TG0: item['game']['IOR_H2C0'],
					MB2TG1: item['game']['IOR_H2C1'],
					MB3TG0: item['game']['IOR_H3C0'],
					MB3TG1: item['game']['IOR_H3C1'],
					MB3TG2: item['game']['IOR_H3C2'],
					MB4TG0: item['game']['IOR_H4C0'],
					MB4TG1: item['game']['IOR_H4C1'],
					MB4TG2: item['game']['IOR_H4C2'],
					MB4TG3: item['game']['IOR_H4C3'],
					MB0TG0: item['game']['IOR_H0C0'],
					MB1TG1: item['game']['IOR_H1C1'],
					MB2TG2: item['game']['IOR_H2C2'],
					MB3TG3: item['game']['IOR_H3C3'],
					MB4TG4: item['game']['IOR_H4C4'],
					MB0TG1: item['game']['IOR_H0C1'],
					MB0TG2: item['game']['IOR_H0C2'],
					MB1TG2: item['game']['IOR_H1C2'],
					MB0TG3: item['game']['IOR_H0C3'],
					MB1TG3: item['game']['IOR_H1C3'],
					MB2TG3: item['game']['IOR_H2C3'],
					MB0TG4: item['game']['IOR_H0C4'],
					MB1TG4: item['game']['IOR_H1C4'],
					MB2TG4: item['game']['IOR_H2C4'],
					MB3TG4: item['game']['IOR_H3C4'],
					UP5: item['game']['IOR_OVH'],
					MID: item['game']['GID'],
					PD_Show: 1,
				};
				itemList.push(data);
			})
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.getFT_CORRECT_SCORE_EARLY = async (thirdPartyAuthData, data) => {
	try {
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		if (data.field == "cp1") {
			formData.append("date", 1);
		} else {
			formData.append("date", "all");
		}
		formData.append("gtype", "ft");
		formData.append("showtype", "early");
		formData.append("rtype", "pd");
		formData.append("ltype", 3);
		formData.append("lid", data.lids);
		if (data.field != "") {
			formData.append("field", data.field);
			formData.append("action", "clickCoupon");
		} else {
			formData.append("action", "click_league");			
		}
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("correctScore:====================", result);
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			tempList.map(async item => {
				let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
				let m_time = item['game']['DATETIME'].split(" ")[1];
				let time = m_time.split(":")[0];
				let temp_minute = m_time.split(":")[1];
				let minute = temp_minute.substring(0, temp_minute.length - 1);
				var lastChar = temp_minute.substr(temp_minute.length - 1);
				if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
					Type: "FT",
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],			
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					M_League: item['game']['LEAGUE'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					M_Date: m_date,
					M_Time: m_time,
					M_Start: m_start,
					MB1TG0: item['game']['IOR_H1C0'],
					MB2TG0: item['game']['IOR_H2C0'],
					MB2TG1: item['game']['IOR_H2C1'],
					MB3TG0: item['game']['IOR_H3C0'],
					MB3TG1: item['game']['IOR_H3C1'],
					MB3TG2: item['game']['IOR_H3C2'],
					MB4TG0: item['game']['IOR_H4C0'],
					MB4TG1: item['game']['IOR_H4C1'],
					MB4TG2: item['game']['IOR_H4C2'],
					MB4TG3: item['game']['IOR_H4C3'],
					MB0TG0: item['game']['IOR_H0C0'],
					MB1TG1: item['game']['IOR_H1C1'],
					MB2TG2: item['game']['IOR_H2C2'],
					MB3TG3: item['game']['IOR_H3C3'],
					MB4TG4: item['game']['IOR_H4C4'],
					MB0TG1: item['game']['IOR_H0C1'],
					MB0TG2: item['game']['IOR_H0C2'],
					MB1TG2: item['game']['IOR_H1C2'],
					MB0TG3: item['game']['IOR_H0C3'],
					MB1TG3: item['game']['IOR_H1C3'],
					MB2TG3: item['game']['IOR_H2C3'],
					MB0TG4: item['game']['IOR_H0C4'],
					MB1TG4: item['game']['IOR_H1C4'],
					MB2TG4: item['game']['IOR_H2C4'],
					MB3TG4: item['game']['IOR_H3C4'],
					UP5: item['game']['IOR_OVH'],
					MID: item['game']['GID'],
					PD_Show: 1,
				};
				itemList.push(data);
			})
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.getFT_CORRECT_SCORE_PARLAY = async (thirdPartyAuthData, data) => {
	try {
		let itemList = [];
		let tempList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		if (data.field == "cp1") {
			formData.append("p3type", "RP3");
			formData.append("date", "all");
		} else if(data.field == "cp2") {
			formData.append("p3type", "P3");
			formData.append("date", 0);
		} else if(data.field == "HotGame_FT_lid_1" || data.field == "HotGame_FT_lid_2") {
			formData.append("p3type", "ALL");
			formData.append("date", "all");
		} else if(data.field == "HotGame_FT_lid_2") {
			formData.append("p3type", "");
			formData.append("date", "all");
		}
		formData.append("gtype", "ft");
		formData.append("showtype", "parlay");
		formData.append("rtype", "pd");
		formData.append("ltype", 3);
		formData.append("lid", data.lids);
		if (data.field != "") {
			formData.append("field", data.field);
			formData.append("action", "clickCoupon");
		} else {
			formData.append("action", "click_league");			
		}
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("correctScore:====================", result);
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			tempList.map(async item => {
				// console.log(item);
				let m_start = "";
				let result = {};
				if (data.field === "cp1") {
					m_start = item['game']['RETIMESET'];
					result = {
						Type: "FT",
						ECID: item['game']['ECID'],
						LID: item['game']['LID'],			
						MB_Ball: item['game']['SCORE_H'],
						TG_Ball: item['game']['SCORE_C'],
						MB_Team: item['game']['TEAM_H'],
						TG_Team: item['game']['TEAM_C'],
						M_League: item['game']['LEAGUE'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						MB1TG0: item['game']['IOR_RH1C0'],
						MB2TG0: item['game']['IOR_RH2C0'],
						MB2TG1: item['game']['IOR_RH2C1'],
						MB3TG0: item['game']['IOR_RH3C0'],
						MB3TG1: item['game']['IOR_RH3C1'],
						MB3TG2: item['game']['IOR_RH3C2'],
						MB4TG0: item['game']['IOR_RH4C0'],
						MB4TG1: item['game']['IOR_RH4C1'],
						MB4TG2: item['game']['IOR_RH4C2'],
						MB4TG3: item['game']['IOR_RH4C3'],
						MB0TG0: item['game']['IOR_RH0C0'],
						MB1TG1: item['game']['IOR_RH1C1'],
						MB2TG2: item['game']['IOR_RH2C2'],
						MB3TG3: item['game']['IOR_RH3C3'],
						MB4TG4: item['game']['IOR_RH4C4'],
						MB0TG1: item['game']['IOR_RH0C1'],
						MB0TG2: item['game']['IOR_RH0C2'],
						MB1TG2: item['game']['IOR_RH1C2'],
						MB0TG3: item['game']['IOR_RH0C3'],
						MB1TG3: item['game']['IOR_RH1C3'],
						MB2TG3: item['game']['IOR_RH2C3'],
						MB0TG4: item['game']['IOR_RH0C4'],
						MB1TG4: item['game']['IOR_RH1C4'],
						MB2TG4: item['game']['IOR_RH2C4'],
						MB3TG4: item['game']['IOR_RH3C4'],
						UP5: item['game']['IOR_ROVH'],
						MID: item['game']['GID'],
						M_Start: m_start,
						PD_Show: 1,
					};

				} else {

					let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
					let m_time = item['game']['DATETIME'].split(" ")[1];
					let time = m_time.split(":")[0];
					let temp_minute = m_time.split(":")[1];
					let minute = temp_minute.substring(0, temp_minute.length - 1);
					var lastChar = temp_minute.substr(temp_minute.length - 1);
					if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
					}
					m_start = m_date + " " + time + ":" + minute;
					m_time = time + ":" + minute;

					result = {
						Type: "FT",
						ECID: item['game']['ECID'],
						LID: item['game']['LID'],			
						MB_Ball: item['game']['SCORE_H'],
						TG_Ball: item['game']['SCORE_C'],
						MB_Team: item['game']['TEAM_H'],
						TG_Team: item['game']['TEAM_C'],
						M_League: item['game']['LEAGUE'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						M_Start: m_start,
						MB1TG0: item['game']['IOR_H1C0'],
						MB2TG0: item['game']['IOR_H2C0'],
						MB2TG1: item['game']['IOR_H2C1'],
						MB3TG0: item['game']['IOR_H3C0'],
						MB3TG1: item['game']['IOR_H3C1'],
						MB3TG2: item['game']['IOR_H3C2'],
						MB4TG0: item['game']['IOR_H4C0'],
						MB4TG1: item['game']['IOR_H4C1'],
						MB4TG2: item['game']['IOR_H4C2'],
						MB4TG3: item['game']['IOR_H4C3'],
						MB0TG0: item['game']['IOR_H0C0'],
						MB1TG1: item['game']['IOR_H1C1'],
						MB2TG2: item['game']['IOR_H2C2'],
						MB3TG3: item['game']['IOR_H3C3'],
						MB4TG4: item['game']['IOR_H4C4'],
						MB0TG1: item['game']['IOR_H0C1'],
						MB0TG2: item['game']['IOR_H0C2'],
						MB1TG2: item['game']['IOR_H1C2'],
						MB0TG3: item['game']['IOR_H0C3'],
						MB1TG3: item['game']['IOR_H1C3'],
						MB2TG3: item['game']['IOR_H2C3'],
						MB0TG4: item['game']['IOR_H0C4'],
						MB1TG4: item['game']['IOR_H1C4'],
						MB2TG4: item['game']['IOR_H2C4'],
						MB3TG4: item['game']['IOR_H3C4'],
						UP5: item['game']['IOR_OVH'],
						MID: item['game']['GID'],
						PD_Show: 1,
					};
				}
				itemList.push(result);
			})
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.getFT_CORRECT_SCORE_FAVORITE = async (thirdPartyAuthData, data) => {
	try {
		let tempList = [];
		let ecidStr = "";
		for (let i = 0; i < data.length; i++) {
			if (i == data.length - 1) {
				ecidStr += data[i]["ecid"]
			} else {
				ecidStr += data[i]["ecid"] + "|"
			}
		}
		let itemList = [];
		let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
		let thirdPartyUrl = "";
		let version = thirdPartyAuthData["version"];
		let uID = thirdPartyAuthData["uid"];
		thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
		let formData = new FormData();
		formData.append("p", "get_game_list");
		formData.append("uid", uID);
		formData.append("ver", version);
		formData.append("langx", "zh-cn");
		formData.append("p3type", "");
		formData.append("date", "");
		formData.append("gtype", "ft");
		formData.append("showtype", "mygame");
		formData.append("rtype", "pd");
		formData.append("ltype", 3);
		formData.append("sorttype", "L");
		formData.append("specialClick", "");
		formData.append("isFantasy", "N");
		formData.append("ts", new Date().getTime());
		formData.append("ecid_str", ecidStr);
		response = await axios.post(thirdPartyUrl, formData);
		if (response.status === 200) {
			let result = parser.parse(response.data);
			// console.log("getFT_CORRECT_SCORE_FAVORITE:====================", result['serverresponse']);
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount === 0 || result['serverresponse']['code'] === "error") return null;
			if (totalDataCount > 1) {
				tempList = [...result['serverresponse']['ec']];
			} else {
				tempList.push(result['serverresponse']['ec']);
			}
			tempList.map(async item => {
				let m_start = "";
				if (item['game']['RETIMESET'] != null && item['game']['RETIMESET'] != undefined) {
					m_start = item['game']['RETIMESET'];
				} else {
					let m_date = moment().format('YYYY') + "-" + item['game']['DATETIME'].split(" ")[0];
					let m_time = item['game']['DATETIME'].split(" ")[1];
					let time = m_time.split(":")[0];
					let temp_minute = m_time.split(":")[1];
					let minute = temp_minute.substring(0, temp_minute.length - 1);
					var lastChar = temp_minute.substr(temp_minute.length - 1);
					if (lastChar == "p") {
						if (Number(time) !== 12) {
							time = Number(time) + 12;
						}
					}
					m_start = m_date + " " + time + ":" + minute;
					m_time = time + ":" + minute;					
				}
				let data = {
					Type: "FT",
					showType: item['myGame'],
					ECID: item['game']['ECID'],
					LID: item['game']['LID'],			
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					MB_Team: item['game']['TEAM_H'],
					TG_Team: item['game']['TEAM_C'],
					M_League: item['game']['LEAGUE'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					M_Start: m_start,
					MB1TG0: item['game']['IOR_H1C0'],
					MB2TG0: item['game']['IOR_H2C0'],
					MB2TG1: item['game']['IOR_H2C1'],
					MB3TG0: item['game']['IOR_H3C0'],
					MB3TG1: item['game']['IOR_H3C1'],
					MB3TG2: item['game']['IOR_H3C2'],
					MB4TG0: item['game']['IOR_H4C0'],
					MB4TG1: item['game']['IOR_H4C1'],
					MB4TG2: item['game']['IOR_H4C2'],
					MB4TG3: item['game']['IOR_H4C3'],
					MB0TG0: item['game']['IOR_H0C0'],
					MB1TG1: item['game']['IOR_H1C1'],
					MB2TG2: item['game']['IOR_H2C2'],
					MB3TG3: item['game']['IOR_H3C3'],
					MB4TG4: item['game']['IOR_H4C4'],
					MB0TG1: item['game']['IOR_H0C1'],
					MB0TG2: item['game']['IOR_H0C2'],
					MB1TG2: item['game']['IOR_H1C2'],
					MB0TG3: item['game']['IOR_H0C3'],
					MB1TG3: item['game']['IOR_H1C3'],
					MB2TG3: item['game']['IOR_H2C3'],
					MB0TG4: item['game']['IOR_H0C4'],
					MB1TG4: item['game']['IOR_H1C4'],
					MB2TG4: item['game']['IOR_H2C4'],
					MB3TG4: item['game']['IOR_H3C4'],
					UP5: item['game']['IOR_OVH'],
					MID: item['game']['GID'],
					PD_Show: 1,
				};
				itemList.push(data);
			})
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

function sleep (milliseconds) {
  	return new Promise((resolve) => setTimeout(resolve, milliseconds))
}
