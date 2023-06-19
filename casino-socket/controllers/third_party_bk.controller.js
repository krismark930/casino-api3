const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { MATCH_SPORTS } = require('../api');
const { SAVE_BK_INPLAY } = require('../api');
const { SAVE_BK_DEFAULT_TODAY } = require('../api');
const { MATCH_CROWN } = require('../api');
const { SAVE_BK_DEFAULT_PARLAY } = require('../api');

var FormData = require('form-data');

const { XMLParser } = require("fast-xml-parser");

const options = {
    ignoreAttributes : false,
    attributeNamePrefix : ""
};

const parser = new XMLParser(options);

var moment = require('moment');

exports.getBK_MORE_TODAY = async (thirdPartyAuthData, data) => {

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
		formData.append("gtype", "bk");
		formData.append("showtype", data["showtype"]);
		formData.append("specialClick", "");
		formData.append("mode", "");
		formData.append("filter", "Main");
		formData.append("lid", data["lid"]);
		formData.append("gid", data["gid"]);
		formData.append("ltype", 3);
		formData.append("isRB", "N");
		formData.append("ts", new Date().getTime());

		response = await axios.post(thirdPartyUrl, formData);

		let itemList = [];

		if (response.status === 200) {

			let result = parser.parse(response.data);

			// console.log(result);

			if (result["serverresponse"]["code"] == "noData") return itemList;

			let tempList = [];

			if (Array.isArray(result["serverresponse"]["game"])) {

				tempList = result["serverresponse"]["game"];

			} else {

				tempList.push(result["serverresponse"]["game"]);
			}

			await Promise.all(tempList.map(async item => {
				if (item["gopen"] === "Y") {
					// console.log("getBK_MORE_TODAY:======================", item)
					let m_date = item['datetime'].split(" ")[0];
					let m_time = item['datetime'].split(" ")[1];
					let m_start = item['datetime'];

					if (item["ratio_o"] != null && item["ratio_o"].toString() != "") {
						if (item["ratio_o"].toString().substring(0, 1) != "O") item["ratio_o"] = "O" + item["ratio_o"];
						if (item["ratio_u"].toString().substring(0, 1) != "U") item["ratio_u"] = "U" + item["ratio_u"];
					}

					let tempData = {
						Type: 'BK',
						LID: data["lid"],
						ECID: item['gidm'],
						MID: item['gid'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						S_Show: 1,
						MB_Team: item['team_h'],
						TG_Team: item['team_c'],
						MB_Team_tw: item['team_h'],
						TG_Team_tw: item['team_c'],
						MB_Team_en: item['team_h'],
						TG_Team_en: item['team_c'],
						M_League: item['league'],
						M_League_tw: item['league'],
						M_League_en: item['league'],
						MB_MID: item['gnum_h'],
						TG_MID: item['gnum_c'],
						// sw_R == "Y"
						M_LetB: item['ratio'] == null ? "" : item['ratio'],
						MB_LetB_Rate: item['ior_RH'] == null ? 0 : item['ior_RH'],
						TG_LetB_Rate: item['ior_RC'] == null ? 0 : item['ior_RC'],
						// sw_OU == "Y"
						MB_Dime: item['ratio_o'] == null ? "" : item['ratio_o'],
						TG_Dime: item['ratio_u'] == null ? "" : item['ratio_u'],
						MB_Dime_Rate: item['ior_OUH'] == null ? 0 : item['ior_OUH'],
						TG_Dime_Rate: item['ior_OUC'] == null ? 0 : item['ior_OUC'],
						// sw_EO == "Y"
						S_Single_Rate: item["ior_EOO"] == null ? 0 : item["ior_EOO"],
						S_Double_Rate: item["ior_EOE"] == null ? 0 : item["ior_EOE"],

						// MB_Points_1: item['ratio_ouho'] == null ? "" : item['ratio_ouho'],
						// TG_Points_1: item['ratio_ouhu'] == null ? "" : item['ratio_ouhu'],
						// MB_Points_Rate_1: item['ior_OUHO'] == null ? 0 : item['ior_OUHO'],
						// TG_Points_Rate_1: item['ior_OUHU'] == null ? 0 : item['ior_OUHU'],
						// MB_Points_2: item['ratio_ouco'] == null ? "" : item['ratio_ouco'],
						// TG_Points_2: item['ratio_oucu'] == null ? "" : item['ratio_oucu'],
						// MB_Points_Rate_2: item['ior_OUCO'] == null ? 0 : item['ior_OUCO'],
						// TG_Points_Rate_2: item['ior_OUCU'] == null ? 0 : item['ior_OUCU'],

						FLAG_CLASS: data['flag_class'] == null ? "" : data['flag_class'],
						Eventid: item['eventid'] == null ? "" : item['eventid'],
						ShowTypeR: item["strong"] == "" ? "H" : item['strong'],
					};
					itemList.push(tempData);					
				}
			}));

			return itemList;
		}

		return itemList;

	} catch(e) {
		console.log(e)
	}
}

exports.getBK_MORE_PARLAY = async (thirdPartyAuthData, data) => {

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
		formData.append("gtype", "bk");
		formData.append("showtype", data["showtype"]);
		formData.append("specialClick", "");
		formData.append("mode", "");
		formData.append("filter", "Main");
		formData.append("lid", data["lid"]);
		formData.append("gid", data["gid"]);
		formData.append("ltype", 3);
		formData.append("isRB", "N");
		formData.append("ts", new Date().getTime());

		response = await axios.post(thirdPartyUrl, formData);

		let itemList = [];

		if (response.status === 200) {

			let result = parser.parse(response.data);

			// console.log(result);

			if (result["serverresponse"]["code"] == "noData") return itemList;

			let tempList = [];

			if (Array.isArray(result["serverresponse"]["game"])) {

				tempList = result["serverresponse"]["game"];

			} else {

				tempList.push(result["serverresponse"]["game"]);
			}

			await Promise.all(tempList.map(async item => {
				if (item["gopen"] === "Y") {
					// console.log("getBK_MORE_TODAY:======================", item)
					let m_date = item['datetime'].split(" ")[0];
					let m_time = item['datetime'].split(" ")[1];
					let m_start = item['datetime'];

					if (item["ratio_o"] != null && item["ratio_o"].toString() != "") {
						if (item["ratio_o"].toString().substring(0, 1) != "O") item["ratio_o"] = "O" + item["ratio_o"];
						if (item["ratio_u"].toString().substring(0, 1) != "U") item["ratio_u"] = "U" + item["ratio_u"];
					}

					let tempData = {
						Type: 'BK',
						LID: data["lid"],
						ECID: item['gidm'],
						MID: item['gid'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						P3_Show: 1,
						MB_Team: item['team_h'],
						TG_Team: item['team_c'],
						MB_Team_tw: item['team_h'],
						TG_Team_tw: item['team_c'],
						MB_Team_en: item['team_h'],
						TG_Team_en: item['team_c'],
						M_League: item['league'],
						M_League_tw: item['league'],
						M_League_en: item['league'],
						MB_MID: item['gnum_h'],
						TG_MID: item['gnum_c'],
						// sw_R == "Y"
						M_P_LetB: item['ratio'] == null ? "" : item['ratio'],
						MB_P_LetB_Rate: item['ior_RH'] == null ? 0 : item['ior_RH'],
						TG_P_LetB_Rate: item['ior_RC'] == null ? 0 : item['ior_RC'],
						// sw_OU == "Y"
						MB_P_Dime: item['ratio_o'] == null ? "" : item['ratio_o'],
						TG_P_Dime: item['ratio_u'] == null ? "" : item['ratio_u'],
						MB_P_Dime_Rate: item['ior_OUH'] == null ? 0 : item['ior_OUH'],
						TG_P_Dime_Rate: item['ior_OUC'] == null ? 0 : item['ior_OUC'],
						// sw_EO == "Y"
						S_P_Single_Rate: item["ior_EOO"] == null ? 0 : item["ior_EOO"],
						S_P_Double_Rate: item["ior_EOE"] == null ? 0 : item["ior_EOE"],

						// MB_Points_1: item['ratio_ouho'] == null ? "" : item['ratio_ouho'],
						// TG_Points_1: item['ratio_ouhu'] == null ? "" : item['ratio_ouhu'],
						// MB_Points_Rate_1: item['ior_OUHO'] == null ? 0 : item['ior_OUHO'],
						// TG_Points_Rate_1: item['ior_OUHU'] == null ? 0 : item['ior_OUHU'],
						// MB_Points_2: item['ratio_ouco'] == null ? "" : item['ratio_ouco'],
						// TG_Points_2: item['ratio_oucu'] == null ? "" : item['ratio_oucu'],
						// MB_Points_Rate_2: item['ior_OUCO'] == null ? 0 : item['ior_OUCO'],
						// TG_Points_Rate_2: item['ior_OUCU'] == null ? 0 : item['ior_OUCU'],

						FLAG_CLASS: data['flag_class'] == null ? "" : data['flag_class'],
						Eventid: item['eventid'] == null ? "" : item['eventid'],
						ShowTypeP: item['strong'],
					};
					itemList.push(tempData);					
				}
			}));

			return itemList;
		}

		return itemList;

	} catch(e) {
		console.log(e)
	}
}

exports.getBK_MORE_INPLAY = async (thirdPartyAuthData, data) => {

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
		formData.append("gtype", "bk");
		formData.append("showtype", data["showtype"]);
		formData.append("specialClick", "");
		formData.append("mode", "");
		formData.append("filter", "Main");
		formData.append("lid", data["lid"]);
		formData.append("gid", data["gid"]);
		formData.append("ltype", 3);
		formData.append("isRB", "Y");
		formData.append("ts", new Date().getTime());

		response = await axios.post(thirdPartyUrl, formData);

		let itemList = [];

		if (response.status === 200) {

			let result = parser.parse(response.data);

			// console.log(result);

			if (result["serverresponse"]["code"] == "noData") return itemList;

			let tempList = [];

			if (Array.isArray(result["serverresponse"]["game"])) {

				tempList = result["serverresponse"]["game"];

			} else {

				tempList.push(result["serverresponse"]["game"]);
			}

			await Promise.all(tempList.map(async item => {
				if (item["gopen"] === "Y") {

					// console.log("getBK_MORE_INPLAY:======================", item)

					let m_date = item['datetime'].split(" ")[0];
					let m_time = item['datetime'].split(" ")[1];
					let m_start = item['datetime'];

					if (item["ratio_rouo"] != null && item["ratio_rouo"].toString() != "") {
						if (item["ratio_rouo"].toString().substring(0, 1) != "O") item["ratio_rouo"] = "O" + item["ratio_rouo"];
						if (item["ratio_rouu"].toString().substring(0, 1) != "U") item["ratio_rouu"] = "U" + item["ratio_rouu"];
					}

					let tempData = {
						Type: 'BK',
						LID: data["lid"],
						ECID: item['gidm'],
						MID: item['gid'],
						M_Date: m_date,
						M_Time: m_time,
						M_Start: m_start,
						Retime: item['re_time'].split("^")[1],
						MB_Team: item['team_h'],
						TG_Team: item['team_c'],
						MB_Team_tw: item['team_h'],
						TG_Team_tw: item['team_c'],
						MB_Team_en: item['team_h'],
						TG_Team_en: item['team_c'],
						M_League: item['league'],
						M_League_tw: item['league'],
						M_League_en: item['league'],
						MB_MID: item['gnum_h'],
						TG_MID: item['gnum_c'],						
						MB_Ball: item['score_h'] == null || item['score_h'] == "" ? 0 : item['score_h'],
						TG_Ball: item['score_c'] == null || item['score_c'] == "" ? 0 : item['score_c'],
						NOW_SESSION: item['session'],
						// sw_RE == "Y"
						M_LetB_RB: item['ratio_re'] == null ? "" : item['ratio_re'],
						MB_LetB_Rate_RB: item['ior_REH'] == null ? 0 : item['ior_REH'],
						TG_LetB_Rate_RB: item['ior_REC'] == null ? 0 : item['ior_REC'],
						// sw_ROU == "Y"
						MB_Dime_RB: item['ratio_rouo'] == null ? "" : item['ratio_rouo'],
						TG_Dime_RB: item['ratio_rouu'] == null ? "" : item['ratio_rouu'],
						MB_Dime_Rate_RB: item['ior_ROUH'] == null ? 0 : item['ior_ROUH'],
						TG_Dime_Rate_RB: item['ior_ROUC'] == null ? 0 : item['ior_ROUC'],
						// sw_EO == "Y"
						S_Single_Rate: item["ior_REOO"] == null ? 0 : item["ior_REOO"],
						S_Double_Rate: item["ior_REOE"] == null ? 0 : item["ior_REOE"],
						FLAG_CLASS: data['flag_class'] == null ? "" : data['flag_class'],
						Eventid: item['eventid'] == null ? "" : item['eventid'],
						Hot: item['hot'] == 'Y'? 1 : 0,
						Play: 1,
						RB_Show: 1,
						ShowTypeRB: item["strong"] == "" ? "H" : item['strong'],
					};
					itemList.push(tempData);					
				}
			}));

			return itemList;
		}

		return itemList;

	} catch(e) {
		console.log(e)
	}
}

exports.getBK_MAIN_FAVORITE = async (thirdPartyAuthData, data) => {
	// console.log("getBK_MAIN_FAVORITE", data);
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
		formData.append("gtype", "bk");
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
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount === 0 || result['serverresponse']['code'] === "error") return null;
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
				if (item['myGame'] === "rb") {
					let data = {
						showType: item['myGame'],
						Type: 'BK',
						Retime: item['game']['TIMER'],
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
						MB_Ball: item['game']['SCORE_H'],
						TG_Ball: item['game']['SCORE_C'],
						NOW_SESSION: item['game']['NOWSESSION'],
						M_LetB_RB: item['game']['RATIO_RE'] == null ? "" : item['game']['RATIO_RE'],
						MB_LetB_Rate_RB: item['game']['IOR_REH'] == null ? 0 : item['game']['IOR_REH'],
						TG_LetB_Rate_RB: item['game']['IOR_REC'] == null ? 0 : item['game']['IOR_REC'],
						MB_Dime_RB: item['game']['RATIO_ROUO'] == null ? "" : item['game']['RATIO_ROUO'],
						TG_Dime_RB: item['game']['RATIO_ROUU'] == null ? "" : item['game']['RATIO_ROUU'],
						MB_Dime_Rate_RB: item['game']['IOR_ROUC'] == null ? 0 : item['game']['IOR_ROUC'],
						TG_Dime_Rate_RB: item['game']['IOR_ROUH'] == null ? 0 : item['game']['IOR_ROUH'],
						// MB_Points_RB_1: item['game']['RATIO_ROUHO'] == null ? "" : item['game']['RATIO_ROUHO'],
						// TG_Points_RB_1: item['game']['RATIO_ROUHU'] == null ? "" : item['game']['RATIO_ROUHU'],
						// MB_Points_Rate_RB_1: item['game']['IOR_ROUHO'] == null ? 0 : item['game']['IOR_ROUHO'],
						// TG_Points_Rate_RB_1: item['game']['IOR_ROUHU'] == null ? 0 : item['game']['IOR_ROUHU'],
						// MB_Points_RB_2: item['game']['RATIO_ROUCO'] == null ? "" : item['game']['RATIO_ROUCO'],
						// TG_Points_RB_2: item['game']['RATIO_ROUCU'] == null ? "" : item['game']['RATIO_ROUCU'],
						// MB_Points_Rate_RB_2: item['game']['IOR_ROUCO'] == null ? 0 : item['game']['IOR_ROUCO'],
						// TG_Points_Rate_RB_2: item['game']['IOR_ROUCU'] == null ? 0 : item['game']['IOR_ROUCU'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
						MID: item['game']['GID'],
						RB_Show: 1,
						ShowTypeRB: item["strong"],
					};
					itemList.push(data);						
				} else {
					let data = {
						showType: item['myGame'],
						Type: 'BK',
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
						M_LetB: item['game']['RATIO_R'] == null ? "" : item['game']['RATIO_R'],
						MB_LetB_Rate: item['game']['IOR_RH'] == null ? 0 : item['game']['IOR_RH'],
						TG_LetB_Rate: item['game']['IOR_RC'] == null ? 0 : item['game']['IOR_RC'],
						MB_Dime: item['game']['RATIO_OUO'] == null ? "" : item['game']['RATIO_OUO'],
						TG_Dime: item['game']['RATIO_OUU'] == null ? "" : item['game']['RATIO_OUU'],
						MB_Dime_Rate: item['game']['IOR_OUC'] == null ? 0 : item['game']['IOR_OUC'],
						TG_Dime_Rate: item['game']['IOR_OUH'] == null ? 0 : item['game']['IOR_OUH'],
						// MB_Points_1: item['game']['RATIO_OUHO'] == null ? "" : item['game']['RATIO_OUHO'],
						// TG_Points_1: item['game']['RATIO_OUHU'] == null ? "" : item['game']['RATIO_OUHU'],
						// MB_Points_Rate_1: item['game']['IOR_OUHO'] == null ? 0 : item['game']['IOR_OUHO'],
						// TG_Points_Rate_1: item['game']['IOR_OUHU'] == null ? 0 : item['game']['IOR_OUHU'],
						// MB_Points_2: item['game']['RATIO_OUCO'] == null ? "" : item['game']['RATIO_OUCO'],
						// TG_Points_2: item['game']['RATIO_OUCU'] == null ? "" : item['game']['RATIO_OUCU'],
						// MB_Points_Rate_2: item['game']['IOR_OUCO'] == null ? 0 : item['game']['IOR_OUCO'],
						// TG_Points_Rate_2: item['game']['IOR_OUCU'] == null ? 0 : item['game']['IOR_OUCU'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
						MID: item['game']['GID'],
						S_Show: 1,
						ShowTypeR: item["strong"],
					};
					itemList.push(data);						
				}
			}));
			return itemList;
		}
		return itemList;
	} catch(e) {
		// console.log(e)
	}
}

exports.dispatchBK_MAIN_FAVORITE = (itemList) => {
	itemList.map(async item => {
		let response;
		try {
			if (item["showType"] === "rb") {
				response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_BK_INPLAY}`, item);
			} else {
				response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_BK_DEFAULT_TODAY}`, item);			
			}
			// console.log("response==============================", response.data);
		} catch(err) {
			// console.log("err===================", err.response);
		}
	})
}

exports.getBK_LEAGUE_CHAMPION = async (thirdPartyAuthData) => {
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
		formData.append("gtype", "BK");
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

exports.getBK_MAIN_CHAMPION = async (thirdPartyAuthData, data) => {
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
		formData.append("gtype", "BK");
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
		// console.log(e)
	}
}

exports.dispatchBK_MAIN_CHAMPION = (itemList) => {
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
					M_Area: "",
					M_Rate: ioratio,
					Gid: rtype,
					mcount: gamecount,
					Gtype: 'BK',
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

exports.getBK_MAIN_INPLAY = async (thirdPartyAuthData) => {
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
		formData.append("gtype", "bk");
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
			// console.log("getBK_MAIN_INPLAY: ", result['serverresponse'])
			let totalDataCount = result['serverresponse']['totalDataCount'];
			if (totalDataCount == 0) return itemList;
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

				var reTimeLastChar = item['game']['TIMER'].substring(item['game']['TIMER'].length -1);
				var reTime = item['game']['TIMER'].substring(0, item['game']['TIMER'].length - 1).split(":")[0];
				var reMinute = item['game']['TIMER'].substring(0, item['game']['TIMER'].length - 1).split(":")[1];
				
				if (reTimeLastChar == "p") {
						if (Number(reTime) !== 12) {
							reTime = Number(reTime) + 12;
						}
				}

				var re_time = reTime + ":" + reMinute;

				let data = {
					Type: 'BK',
					Retime: item['game']['TIMER'],
					LID: item['game']['LID'],
					ECID: item['game']['GIDM'],
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
					MB_Ball: item['game']['SCORE_H'],
					TG_Ball: item['game']['SCORE_C'],
					NOW_SESSION: item['game']['NOWSESSION'],
					M_LetB_RB: item['game']['RATIO_RE'] == null ? "" : item['game']['RATIO_RE'],
					MB_LetB_Rate_RB: item['game']['IOR_REH'] == null ? 0 : item['game']['IOR_REH'],
					TG_LetB_Rate_RB: item['game']['IOR_REC'] == null ? 0 : item['game']['IOR_REC'],
					MB_Dime_RB: item['game']['RATIO_ROUO'] == null ? "" : item['game']['RATIO_ROUO'],
					TG_Dime_RB: item['game']['RATIO_ROUU'] == null ? "" : item['game']['RATIO_ROUU'],
					MB_Dime_Rate_RB: item['game']['IOR_ROUC'] == null ? 0 : item['game']['IOR_ROUC'],
					TG_Dime_Rate_RB: item['game']['IOR_ROUH'] == null ? 0 : item['game']['IOR_ROUH'],
					S_Single_Rate: 0,
					S_Double_Rate: 0,

					// MB_Points_RB_1: item['game']['RATIO_ROUHO'] == null ? "" : item['game']['RATIO_ROUHO'],
					// TG_Points_RB_1: item['game']['RATIO_ROUHU'] == null ? "" : item['game']['RATIO_ROUHU'],
					// MB_Points_Rate_RB_1: item['game']['IOR_ROUHO'] == null ? 0 : item['game']['IOR_ROUHO'],
					// TG_Points_Rate_RB_1: item['game']['IOR_ROUHU'] == null ? 0 : item['game']['IOR_ROUHU'],
					// MB_Points_RB_2: item['game']['RATIO_ROUCO'] == null ? "" : item['game']['RATIO_ROUCO'],
					// TG_Points_RB_2: item['game']['RATIO_ROUCU'] == null ? "" : item['game']['RATIO_ROUCU'],
					// MB_Points_Rate_RB_2: item['game']['IOR_ROUCO'] == null ? 0 : item['game']['IOR_ROUCO'],
					// TG_Points_Rate_RB_2: item['game']['IOR_ROUCU'] == null ? 0 : item['game']['IOR_ROUCU'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
					Hot: item['game']['HOT'] == 'Y'? 1 : 0,
					Play: item['game']['PLAY'] == 'Y'? 1 : 0,
					MID: item['game']['GID'],
					ShowTypeRB: item['game']['STRONG'] == "" ? "H" : item['game']['STRONG'],
					RB_Show: 1,
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

exports.dispatchBK_MAIN_INPLAY = (itemList) => {
	itemList.map(async item => {
		try {
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_BK_INPLAY}`, item);
			// console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err);
		}
	})
}

exports.getBK_LEAGUE_TODAY = async (thirdPartyAuthData) => {
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
		formData.append("gtype", "BK");
		formData.append("FS", "N");
		formData.append("showtype", "ft");
		formData.append("date", 0);
		formData.append("ts", new Date().getTime());
		formData.append("nocp", "N");

		response = await axios.post(thirdPartyUrl, formData);

		if (response.status === 200) {
			var result = parser.parse(response.data);
			// console.log("getBK_LEAGUE_TODAY:=============", result["serverresponse"]);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		// console.log(e)
		return null;
	}
}

exports.getBK_LEAGUE_EARLY = async (thirdPartyAuthData) => {
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
		formData.append("gtype", "BK");
		formData.append("FS", "N");
		formData.append("showtype", "fu");
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

exports.getBK_LEAGUE_PARLAY = async (thirdPartyAuthData) => {
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
		formData.append("gtype", "BK");
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

exports.getBK_MAIN_TODAY = async (thirdPartyAuthData, data) => {
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
		formData.append("gtype", "bk");
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

				// console.log("getBK_MAIN_TODAY:======================", item['game'])

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
					Type: 'BK',
					LID: item['game']['LID'],
					ECID: item['game']['GIDM'],
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
					M_LetB: item['game']['RATIO_R'] == null ? "" : item['game']['RATIO_R'],
					MB_LetB_Rate: item['game']['IOR_RH'] == null ? 0 : item['game']['IOR_RH'],
					TG_LetB_Rate: item['game']['IOR_RC'] == null ? 0 : item['game']['IOR_RC'],
					MB_Dime: item['game']['RATIO_OUO'] == null ? "" : item['game']['RATIO_OUO'],
					TG_Dime: item['game']['RATIO_OUU'] == null ? "" : item['game']['RATIO_OUU'],
					MB_Dime_Rate: item['game']['IOR_OUC'] == null ? 0 : item['game']['IOR_OUC'],
					TG_Dime_Rate: item['game']['IOR_OUH'] == null ? 0 : item['game']['IOR_OUH'],
					S_Single_Rate: 0,
					S_Double_Rate: 0,

					MB_Points_1: item['game']['RATIO_OUHO'] == null ? "" : item['game']['RATIO_OUHO'],
					TG_Points_1: item['game']['RATIO_OUHU'] == null ? "" : item['game']['RATIO_OUHU'],
					MB_Points_Rate_1: item['game']['IOR_OUHO'] == null ? 0 : item['game']['IOR_OUHO'],
					TG_Points_Rate_1: item['game']['IOR_OUHU'] == null ? 0 : item['game']['IOR_OUHU'],
					MB_Points_2: item['game']['RATIO_OUCO'] == null ? "" : item['game']['RATIO_OUCO'],
					TG_Points_2: item['game']['RATIO_OUCU'] == null ? "" : item['game']['RATIO_OUCU'],
					MB_Points_Rate_2: item['game']['IOR_OUCO'] == null ? 0 : item['game']['IOR_OUCO'],
					TG_Points_Rate_2: item['game']['IOR_OUCU'] == null ? 0 : item['game']['IOR_OUCU'],

					FLAG_CLASS: item['game']['FLAG_CLASS'],
					Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
					ShowTypeR: item['game']['STRONG'] == "" ? "H" : item['game']['STRONG'],
					MID: item['game']['GID'],
					S_Show: 1,
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

exports.getBK_MAIN_EARLY = async (thirdPartyAuthData, data) => {
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
		formData.append("gtype", "bk");
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
				// console.log("CORNER:======================", item['game'])
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
					Type: 'BK',
					LID: item['game']['LID'],
					ECID: item['game']['GIDM'],
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
					M_LetB: item['game']['RATIO_R'] == null ? "" : item['game']['RATIO_R'],
					MB_LetB_Rate: item['game']['IOR_RH'] == null ? 0 : item['game']['IOR_RH'],
					TG_LetB_Rate: item['game']['IOR_RC'] == null ? 0 : item['game']['IOR_RC'],
					MB_Dime: item['game']['RATIO_OUO'] == null ? "" : item['game']['RATIO_OUO'],
					TG_Dime: item['game']['RATIO_OUU'] == null ? "" : item['game']['RATIO_OUU'],
					MB_Dime_Rate: item['game']['IOR_OUC'] == null ? 0 : item['game']['IOR_OUC'],
					TG_Dime_Rate: item['game']['IOR_OUH'] == null ? 0 : item['game']['IOR_OUH'],
					S_Single_Rate: 0,
					S_Double_Rate: 0,

					// MB_Points_1: item['game']['RATIO_OUHO'] == null ? "" : item['game']['RATIO_OUHO'],
					// TG_Points_1: item['game']['RATIO_OUHU'] == null ? "" : item['game']['RATIO_OUHU'],
					// MB_Points_Rate_1: item['game']['IOR_OUHO'] == null ? 0 : item['game']['IOR_OUHO'],
					// TG_Points_Rate_1: item['game']['IOR_OUHU'] == null ? 0 : item['game']['IOR_OUHU'],
					// MB_Points_2: item['game']['RATIO_OUCO'] == null ? "" : item['game']['RATIO_OUCO'],
					// TG_Points_2: item['game']['RATIO_OUCU'] == null ? "" : item['game']['RATIO_OUCU'],
					// MB_Points_Rate_2: item['game']['IOR_OUCO'] == null ? 0 : item['game']['IOR_OUCO'],
					// TG_Points_Rate_2: item['game']['IOR_OUCU'] == null ? 0 : item['game']['IOR_OUCU'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
					MID: item['game']['GID'],
					S_Show: 1,
					ShowTypeR: item["strong"],
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

exports.getBK_MAIN_PARLAY = async (thirdPartyAuthData, data) => {
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
		} else if (data.field === "cp2") {
			formData.append("p3type", "P3");
			formData.append("date", 0);
		}
		formData.append("gtype", "bk");
		formData.append("showtype", "parlay");
		if (data.field === "cp1") {
			formData.append("rtype", "rb");
		} else {
			formData.append("rtype", "r");
		}
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
					Type: 'BK',
					LID: item['game']['LID'],
					ECID: item['game']['GIDM'],
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
					MB_MID: item['game']['GNUM_H'],
					TG_MID: item['game']['GNUM_C'],
					M_P_LetB: item['game']['RATIO_R'] == null ? "" : item['game']['RATIO_R'],
					MB_P_LetB_Rate: item['game']['IOR_RH'] == null ? 0 : item['game']['IOR_RH'],
					TG_P_LetB_Rate: item['game']['IOR_RC'] == null ? 0 : item['game']['IOR_RC'],
					MB_P_Dime: item['game']['RATIO_OUO'] == null ? "" : item['game']['RATIO_OUO'],
					TG_P_Dime: item['game']['RATIO_OUU'] == null ? "" : item['game']['RATIO_OUU'],
					MB_P_Dime_Rate: item['game']['IOR_OUC'] == null ? 0 : item['game']['IOR_OUC'],
					TG_P_Dime_Rate: item['game']['IOR_OUH'] == null ? 0 : item['game']['IOR_OUH'],
					S_P_Single_Rate: 0,
					S_P_Double_Rate: 0,

					// MB_P_Points_1: item['game']['RATIO_OUHO'] == null ? "" : item['game']['RATIO_OUHO'],
					// TG_P_Points_1: item['game']['RATIO_OUHU'] == null ? "" : item['game']['RATIO_OUHU'],
					// MB_P_Points_Rate_1: item['game']['IOR_OUHO'] == null ? 0 : item['game']['IOR_OUHO'],
					// TG_P_Points_Rate_1: item['game']['IOR_OUHU'] == null ? 0 : item['game']['IOR_OUHU'],
					// MB_P_Points_2: item['game']['RATIO_OUCO'] == null ? "" : item['game']['RATIO_OUCO'],
					// TG_P_Points_2: item['game']['RATIO_OUCU'] == null ? "" : item['game']['RATIO_OUCU'],
					// MB_P_Points_Rate_2: item['game']['IOR_OUCO'] == null ? 0 : item['game']['IOR_OUCO'],
					// TG_P_Points_Rate_2: item['game']['IOR_OUCU'] == null ? 0 : item['game']['IOR_OUCU'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
					ShowTypeP: item['game']['STRONG'] == "" ? "H" : item['game']['STRONG'],
					P3_Show: 1,
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

exports.dispatchBK_MAIN_TODAY = (itemList) => {
	itemList.map(async item => {	
		try {
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_BK_DEFAULT_TODAY}`, item);
			console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err);
		}
	})
}

exports.dispatchBK_MAIN_PARLAY = (itemList) => {
	itemList.map(async item => {	
		try {
			response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_BK_DEFAULT_PARLAY}`, item);
			console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err);
		}
	})
}

function sleep (milliseconds) {
  	return new Promise((resolve) => setTimeout(resolve, milliseconds))
}
