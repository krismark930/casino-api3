const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { MATCH_SPORTS } = require('../api');
const { SAVE_BK_INPLAY } = require('../api');
const { SAVE_BK_DEFAULT_TODAY } = require('../api');
const { MATCH_CROWN } = require('../api');
const { SAVE_BK_DEFAULT_PARLAY } = require('../api');

var FormData = require('form-data');
var convert = require('xml-js');
const { XMLParser, XMLBuilder, XMLValidator} = require("fast-xml-parser");
var moment = require('moment');
const parser = new XMLParser();


exports.getBK_MAIN_FAVORITE = async (thirdPartyAuthData, data) => {
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
			// let result = parser.parse(response.data);
			var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
			console.log("FT_FAVORITE: ", result['serverresponse']['ec'])
			if (result['serverresponse']['code'] === "error") return null;
			let totalDataCount = result['serverresponse']['totalDataCount']['_text'];
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
					time = Number(time) + 12;
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				if (item['_attributes']['myGame'] === "rb") {
					let data = {
						showType: item['_attributes']['myGame'],
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
						MB_Points_RB_1: item['game']['RATIO_ROUHO'] == null ? "" : item['game']['RATIO_ROUHO'],
						TG_Points_RB_1: item['game']['RATIO_ROUHU'] == null ? "" : item['game']['RATIO_ROUHU'],
						MB_Points_Rate_RB_1: item['game']['IOR_ROUHO'] == null ? 0 : item['game']['IOR_ROUHO'],
						TG_Points_Rate_RB_1: item['game']['IOR_ROUHU'] == null ? 0 : item['game']['IOR_ROUHU'],
						MB_Points_RB_2: item['game']['RATIO_ROUCO'] == null ? "" : item['game']['RATIO_ROUCO'],
						TG_Points_RB_2: item['game']['RATIO_ROUCU'] == null ? "" : item['game']['RATIO_ROUCU'],
						MB_Points_Rate_RB_2: item['game']['IOR_ROUCO'] == null ? 0 : item['game']['IOR_ROUCO'],
						TG_Points_Rate_RB_2: item['game']['IOR_ROUCU'] == null ? 0 : item['game']['IOR_ROUCU'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
						Hot: item['game']['HOT'] == 'Y'? 1 : 0,
						Play: item['game']['PLAY'] == 'Y'? 1 : 0,
						MID: item['game']['GID'],
						RB_Show: 1,
					};
					itemList.push(data);						
				} else {
					let data = {
						showType: item['_attributes']['myGame'],
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
						MID: item['game']['GID'],
						S_Show: 1,
					};
					itemList.push(data);						
				}
				itemList.push(data);
			}));
			return itemList;
		}
		return itemList;
	} catch(e) {
		console.log(e)
		await sleep(2000);
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
			console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err.response);
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
			var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		console.log(e)
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
		console.log(e)
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
				console.log("dispatchFT_MAIN_CHAMPION: ", response.data);
			}));
		} catch(e) {
			console.log(e);
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
			console.log("1111111111111111111111111111", result['serverresponse'])
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
					time = Number(time) + 12;
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
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
					MB_Points_RB_1: item['game']['RATIO_ROUHO'] == null ? "" : item['game']['RATIO_ROUHO'],
					TG_Points_RB_1: item['game']['RATIO_ROUHU'] == null ? "" : item['game']['RATIO_ROUHU'],
					MB_Points_Rate_RB_1: item['game']['IOR_ROUHO'] == null ? 0 : item['game']['IOR_ROUHO'],
					TG_Points_Rate_RB_1: item['game']['IOR_ROUHU'] == null ? 0 : item['game']['IOR_ROUHU'],
					MB_Points_RB_2: item['game']['RATIO_ROUCO'] == null ? "" : item['game']['RATIO_ROUCO'],
					TG_Points_RB_2: item['game']['RATIO_ROUCU'] == null ? "" : item['game']['RATIO_ROUCU'],
					MB_Points_Rate_RB_2: item['game']['IOR_ROUCO'] == null ? 0 : item['game']['IOR_ROUCO'],
					TG_Points_Rate_RB_2: item['game']['IOR_ROUCU'] == null ? 0 : item['game']['IOR_ROUCU'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
					Hot: item['game']['HOT'] == 'Y'? 1 : 0,
					Play: item['game']['PLAY'] == 'Y'? 1 : 0,
					MID: item['game']['GID'],
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
			console.log("response==============================", response.data);
		} catch(err) {
			console.log("err===================", err.response);
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
			// var result = parser.parse(response.data);
			var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
			console.log("getBK_LEAGUE_TODAY:=============", result["serverresponse"]);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		console.log(e)
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
			// var result = parser.parse(response.data);
			var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
			console.log("getFT_LEAGUE_TODAY:=============", result["serverresponse"]);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		console.log(e)
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
			// var result = parser.parse(response.data);
			var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
			console.log("getFT_LEAGUE_TODAY:=============", result["serverresponse"]);
			return result["serverresponse"];
		}
		return null;
	} catch(e) {
		console.log(e)
		return null;
	}
}

exports.getBK_MAIN_TODAY = async (thirdPartyAuthData, data) => {
	try {
		console.log(data);
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
			console.log("getFT_DEFAULT_TODAY:=========", result['serverresponse'])
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
					time = Number(time) + 12;
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
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
					MID: item['game']['GID'],
					S_Show: 1,
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

exports.getBK_MAIN_EARLY = async (thirdPartyAuthData, data) => {
	try {
		console.log(data);
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
			console.log("getFT_DEFAULT_TODAY:=========", result['serverresponse'])
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
					time = Number(time) + 12;
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
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
					MID: item['game']['GID'],
					S_Show: 1,
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

exports.getBK_MAIN_PARLAY = async (thirdPartyAuthData, data) => {
	try {
		console.log(data);
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
			console.log(result);
			console.log("getFT_DEFAULT_PARLAY:=========", result['serverresponse']['ec'][0]['game']['DATETIME'])
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
					time = Number(time) + 12;
				}
				let m_start = m_date + " " + time + ":" + minute;
				m_time = time + ":" + minute;
				let data = {
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
					M_P_LetB: item['game']['RATIO_R'] == null ? "" : item['game']['RATIO_R'],
					MB_P_LetB_Rate: item['game']['IOR_RH'] == null ? 0 : item['game']['IOR_RH'],
					TG_P_LetB_Rate: item['game']['IOR_RC'] == null ? 0 : item['game']['IOR_RC'],
					MB_P_Dime: item['game']['RATIO_OUO'] == null ? "" : item['game']['RATIO_OUO'],
					TG_P_Dime: item['game']['RATIO_OUU'] == null ? "" : item['game']['RATIO_OUU'],
					MB_P_Dime_Rate: item['game']['IOR_OUC'] == null ? 0 : item['game']['IOR_OUC'],
					TG_P_Dime_Rate: item['game']['IOR_OUH'] == null ? 0 : item['game']['IOR_OUH'],
					MB_P_Points_1: item['game']['RATIO_OUHO'] == null ? "" : item['game']['RATIO_OUHO'],
					TG_P_Points_1: item['game']['RATIO_OUHU'] == null ? "" : item['game']['RATIO_OUHU'],
					MB_P_Points_Rate_1: item['game']['IOR_OUHO'] == null ? 0 : item['game']['IOR_OUHO'],
					TG_P_Points_Rate_1: item['game']['IOR_OUHU'] == null ? 0 : item['game']['IOR_OUHU'],
					MB_P_Points_2: item['game']['RATIO_OUCO'] == null ? "" : item['game']['RATIO_OUCO'],
					TG_P_Points_2: item['game']['RATIO_OUCU'] == null ? "" : item['game']['RATIO_OUCU'],
					MB_P_Points_Rate_2: item['game']['IOR_OUCO'] == null ? 0 : item['game']['IOR_OUCO'],
					TG_P_Points_Rate_2: item['game']['IOR_OUCU'] == null ? 0 : item['game']['IOR_OUCU'],
					FLAG_CLASS: item['game']['FLAG_CLASS'],
					Eventid: item['game']['EVENTID'] == null ? "" : item['game']['EVENTID'],
					MID: item['game']['GID'],
					P3_Show: 1,
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
