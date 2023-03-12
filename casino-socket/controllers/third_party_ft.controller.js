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
var FormData = require('form-data');
var convert = require('xml-js');
const { XMLParser, XMLBuilder, XMLValidator} = require("fast-xml-parser");
var moment = require('moment');
const parser = new XMLParser();

exports.getFT_FS = async () => {
	try {
		let thirdPartyBaseUrl = "";
		let thirdPartyUrl = "";
		let version = "";
		let uID = "";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;
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
				// var result = parser.parse(response.data);
				var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
				if (result['serverresponse']['classifier']['region'][0] == null || result['serverresponse']['classifier']['region'][0] == undefined) {
					let temp_arr = result['classifier']['region'];
					result['classifier']['region'] = [];
					result['classifier']['region'].push($temp_arr);
				}
				let area = "";
				let lid = "";
				console.log(result['serverresponse']['classifier']['region']);
				result['serverresponse']['classifier']['region'].map(country => {
					if (country['_attributes']['name'] != '') {
						area = country['_attributes']['name'];
					}
					for ( let i = 0; i < country['league'].length; i++ ) {
						if (country['league'][i]['_attributes']['id'] != '') {
							lid = country['league'][i]['_attributes']['id'];
						}
						area_arr.push({lid, area});
					}
				})
				var gameList = [];
				var m_area = "";
				await Promise.all(area_arr.map( async item => {
					ts = new Date().getTime() + Math.random(100, 999);
					let m_area = item.area;
					let lid = item.lid;
					let formData = new FormData();
					formData.append("uid", uID);
					formData.append("ver", version);
					formData.append("langx", "zh-cn");
					formData.append("p", "get_game_list_FS");
					formData.append("gtype", "FT");
					formData.append("search", "all");
					formData.append("rtype", "fs");
					formData.append("league_id", lid);
					formData.append("date", version);
					formData.append("special", version);
					response = await axios.post(thirdPartyUrl, formData);
					if (response.status === 200) {
						let result = parser.parse(response.data);
						gameList.push(result['serverresponse']['game']);
						// console.log("2222222222222222", gameList)
					}
				}));
				// console.log("11111111111111111111111", gameList[0]);
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
				gameList.map(item => {
					if(item['gid'] == undefined) return;
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
				});
				rtypes.map(async item => {
					let result = item['result'];
					let rtype = item['rtype'];
					let teams = item['teams'];
					let ioratio = item['ioratio'];
					try {
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
							M_Area: m_area,
							M_Rate: ioratio,
							Gid: rtype,
							mcount: gamecount,
							Gtype: 'FT',
							mshow: gopen,
							mshow2: result
						}
						// console.log("1111111111111111111========", data);
						response = await axios.post(`${BACKEND_BASE_URL}${MATCH_CROWN}/add`, data);
						console.log("final===================", response.data);
					} catch(e) {
						console.log(e);
					}
				})
			}
		}
	} catch(e) {
		console.log(e)
	}
}

exports.getFT_FU_R_TODAY = async () => {
	try {
		let thirdPartyBaseUrl = "https://www.hga030.com";
		let thirdPartyUrl = "";
		let version = "-3ed5-newkeyboard-0303-95881ae5676be2";
		let uID = "hojnotcm27417505l93240b0";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		await sleep(2000);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;
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
				console.log(response.data);
				var lid = "";
				let result = parser.parse(response.data);
				console.log(result);
				if (result['serverresponse']['coupons']['coupon'].length > 0) {
					result['serverresponse']['coupons']['coupon'].map(item => {
						if (item['name'] === "今日赛事") lid = item['lid'];
					})
				} else {
					if (result['serverresponse']['coupons']['coupon']['name'] === "今日赛事") lid = result['serverresponse']['coupons']['coupon']['lid'];
				}
				console.log(lid);
				let lid_array = (lid + ",").split(",");
				await Promise.all(lid_array.map(async lid => {
					let formData = new FormData();
					formData.append("p", "get_game_list");
					formData.append("uid", uID);
					formData.append("ver", version);
					formData.append("langx", "zh-cn");
					formData.append("p3type", "");
					formData.append("date", "all");
					formData.append("gtype", "ft");
					formData.append("showtype", "today");
					formData.append("rtype", "r");
					formData.append("ltype", 3);
					formData.append("lid", lid);
					formData.append("action", "click_league");
					formData.append("sorttype", "L");
					formData.append("specialClick", "");
					formData.append("isFantasy", "N");
					formData.append("ts", new Date().getTime());
					response = await axios.post(thirdPartyUrl, formData);
					if (response.status === 200) {
						let result = parser.parse(response.data);
						let totalDataCount = result['serverresponse']['totalDataCount'];
						if (totalDataCount > 1) {
							result['serverresponse']['ec'].reduce(async (memo, item) => {
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
									ShowTypeHR: item['game']['STRONG'],
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
								};
								try {
									response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_FU_R}`, data);
									await sleep(4000);
									console.log("response==============================", response.data);
								} catch(err) {
									console.log("err===================", err.response);
								}
							});
						} else if(totalDataCount == 1) {
							let item = result['serverresponse']['ec'];
								console.log(item.game);
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
								M_Flat_Rate: item['game']['IOR_MN'] ?? 0,
								M_LetB: item['game']['RATIO_R'],
								MB_LetB_Rate: item['game']['IOR_RH'],
								TG_LetB_Rate: item['game']['IOR_RC'],
								MB_Dime: item['game']['RATIO_OUO'],
								TG_Dime: item['game']['RATIO_OUU'],
								MB_Dime_Rate: item['game']['IOR_OUC'],
								TG_Dime_Rate: item['game']['IOR_OUH'],
								ShowTypeHR: item['game']['STRONG'],
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
							};
							try {
								response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_FU_R}`, data);
								await sleep(4000);
								console.log("response==============================", response.data);
							} catch(err) {
								console.log("err===================", err.response);
							}
						}
					}
				}))
			}
		}
	} catch(e) {
		console.log(e)
		await sleep(4000);
	}
}

exports.getLeagueListALL_TODAY = async () => {
	try {
		let thirdPartyBaseUrl = "https://www.hga030.com";
		let thirdPartyUrl = "";
		let version = "-3ed5-newkeyboard-0303-95881ae5676be2";
		let uID = "hojnotcm27417505l93240b0";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;

			thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;

			formData = new FormData();
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

			let response = await axios.post(thirdPartyUrl, formData);

			if (response.status === 200) {
				let result = parser.parse(response.data);
				return result;
			}
		}
	} catch(e) {
		console.log(e)
	}	
}

exports.getFT_FU_R_INPLAY = async () => {
	try {
		let thirdPartyBaseUrl = "https://www.hga030.com";
		let thirdPartyUrl = "";
		let version = "-3ed5-newkeyboard-0303-95881ae5676be2";
		let uID = "hojnotcm27417505l93240b0";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		await sleep(2000);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;
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
					await Promise.all(result['serverresponse']['ec'].map(async item => {
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
						// console.log("22222222222222", item['game']['IOR_HREH'] ?? 0)
						let m_start = m_date + " " + time + ":" + minute;
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
							ShowTypeRB: item['game']['HSTRONG'] == "" ? "H" : item['game']['HSTRONG'],
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
						try {
							response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_IN_PLAY}`, data);
							// await sleep(2000);
							console.log("response==============================", response.data);
						} catch(err) {
							console.log("err===================", err.response);
						}
					}));
					// let ecIDArray = []
					// result['serverresponse']['ec'].map(item => {
					// 	ecIDArray.push(item['game']['ECID']);
					// })
					// ecIDArray = [...new Set(ecIDArray)];
					// console.log("ecIDArray:====================", ecIDArray);
					// await Promise.all(ecIDArray.map(async ecid => {
					// 	let formData = new FormData();
					// 	formData.append("p", "get_game_OBT");
					// 	formData.append("uid", uID);
					// 	formData.append("ver", version);
					// 	formData.append("langx", "zh-cn");
					// 	formData.append("gtype", "ft");
					// 	formData.append("showtype", "live");
					// 	formData.append("isSpecial", "");
					// 	formData.append("isEarly", "N");
					// 	formData.append("model", "ROU|MIX");
					// 	formData.append("isETWI", "N");
					// 	formData.append("ecid", 6776241);
					// 	formData.append("ltype", 3);
					// 	formData.append("is_rb", "Y");
					// 	formData.append("ts", new Date().getTime());

					// 	response = await axios.post(thirdPartyUrl, formData);

					// 	formData = new FormData();
					// 	formData.append("p", "get_game_OBT");
					// 	formData.append("uid", uID);
					// 	formData.append("ver", version);
					// 	formData.append("langx", "zh-cn");
					// 	formData.append("gtype", "ft");
					// 	formData.append("showtype", "live");
					// 	formData.append("isSpecial", "");
					// 	formData.append("isEarly", "N");
					// 	formData.append("model", "CN");
					// 	formData.append("isETWI", "N");
					// 	formData.append("ecid", ecid);
					// 	formData.append("ltype", 3);
					// 	formData.append("is_rb", "Y");
					// 	formData.append("ts", new Date().getTime());
					// 	formData.append("isClick", "Y");

					// 	let cnResponse = await axios.post(thirdPartyUrl, formData);

					// 	let data = {};

					// 	if (cnResponse.status === 200) {
					// 		let result = parser.parse(cnResponse.data);
					// 		if (result["serverresponse"]["code"] !== "noData") {
					// 			console.log("getGameOBT_CN:===============", result);
					// 			data["RATIO_ROUO_CN"] = result["serverresponse"]["ec"]["game"][0]["RATIO_ROUO"];
					// 			data["RATIO_ROUU_CN"] = result["serverresponse"]["ec"]["game"][0]["RATIO_ROUU"];
					// 			data["IOR_ROUH_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_ROUH"];
					// 			data["IOR_ROUC_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_ROUC"];
					// 			data["RATIO_HROUO_CN"] = result["serverresponse"]["ec"]["game"][0]["RATIO_HROUO"];
					// 			data["RATIO_HROUU_CN"] = result["serverresponse"]["ec"]["game"][0]["RATIO_HROUU"];
					// 			data["IOR_HROUH_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_HROUH"];
					// 			data["IOR_HROUC_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_HROUC"];
					// 			data["STR_ODD_CN"] = result["serverresponse"]["ec"]["game"][0]["STR_ODD"];
					// 			data["STR_EVEN_CN"] = result["serverresponse"]["ec"]["game"][0]["STR_EVEN"];
					// 			data["IOR_REOO_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_REOO"];
					// 			data["IOR_REOE_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_REOE"];
					// 			data["STR_HODD_CN"] = result["serverresponse"]["ec"]["game"][0]["STR_HODD"];
					// 			data["STR_HEVEN_CN"] = result["serverresponse"]["ec"]["game"][0]["STR_HEVEN"];
					// 			data["IOR_HREOO_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_HREOO"];
					// 			data["IOR_HREOE_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_HREOE"];
					// 			data["WTYPE_CN"] = result["serverresponse"]["ec"]["game"][0]["WTYPE_CN"];
					// 			data["IOR_RNCH_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_RNCH"];
					// 			data["IOR_RNCC_CN"] = result["serverresponse"]["ec"]["game"][0]["IOR_RNCC"];
					// 		}
					// 	}

					// 	if (response.status === 200) {
					// 		let result = parser.parse(response.data);
					// 		console.log("getGameOBT:=============", result["serverresponse"]["ec"]["game"]);
					// 		if (result["serverresponse"]["ec"]["game"].length > 0) {
					// 			for (let i = 0; i < result["serverresponse"]["ec"]["game"].length; i++) {
					// 				if (i == 0) {										
					// 					data["MID"] = result["serverresponse"]["ec"]["game"][i]["GID"];
					// 					data["RATIO_RE_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
					// 					data["IOR_REH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
					// 					data["IOR_REC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
					// 					data["RATIO_ROUO_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
					// 					data["RATIO_ROUU_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
					// 					data["IOR_ROUH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
					// 					data["IOR_ROUC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					// 				}
					// 				if (i == 1) {
					// 					data["RATIO_RE_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
					// 					data["IOR_REH_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
					// 					data["IOR_REC_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
					// 					data["RATIO_ROUO_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
					// 					data["RATIO_ROUU_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
					// 					data["IOR_ROUH_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
					// 					data["IOR_ROUC_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					// 				}
					// 				if (i == 2) {
					// 					data["RATIO_RE_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
					// 					data["IOR_REH_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
					// 					data["IOR_REC_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
					// 					data["RATIO_ROUO_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
					// 					data["RATIO_ROUU_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
					// 					data["IOR_ROUH_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
					// 					data["IOR_ROUC_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					// 				}
					// 				if (i == 3) {
					// 					data["RATIO_RE_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
					// 					data["IOR_REH_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
					// 					data["IOR_REC_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
					// 					data["RATIO_ROUO_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
					// 					data["RATIO_ROUU_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
					// 					data["IOR_ROUH_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
					// 					data["IOR_ROUC_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
					// 				}
					// 			}
					// 			try {									
					// 				response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_HDP_OBT}`, data);
					// 				console.log("FT_HDP_OBT_RESPONSE:===============", response.data);
					// 			} catch(err) {
					// 				console.log("FT_HDP_OBT_ERROR:===============", err);
					// 			}
					// 		}
					// 	}
					// }))
				} else if(totalDataCount == 1) {
					let item = result['serverresponse']['ec'];
						console.log(item.game);
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
						ShowTypeRB: item['game']['STRONG'],
						M_LetB_RB: item['game']['RATIO_RE'],
						MB_LetB_Rate_RB: item['game']['IOR_REH'],
						TG_LetB_Rate_RB: item['game']['IOR_REC'],
						MB_Dime_RB: item['game']['RATIO_ROUO'],
						TG_Dime_RB: item['game']['RATIO_ROUU'],
						MB_Dime_Rate_RB: item['game']['IOR_ROUC'],
						TG_Dime_Rate_RB: item['game']['IOR_ROUH'],
						ShowTypeHRB: item['game']['HSTRONG'],
						M_LetB_RB_H: item['game']['RATIO_HRE'],
						MB_LetB_Rate_RB_H: item['game']['IOR_HREH'],
						TG_LetB_Rate_RB_H: item['game']['IOR_HREC'],
						MB_Dime_RB_H: item['game']['RATIO_HROUO'],
						TG_Dime_RB_H: item['game']['RATIO_HROUU'],
						MB_Dime_Rate_RB_H: item['game']['IOR_HROUC'],
						TG_Dime_Rate_RB_H: item['game']['IOR_HROUH'],
						MB_Ball: item['game']['SCORE_H'],
						TG_Ball: item['game']['SCORE_C'],
						MB_Win_Rate_RB: item['game']['IOR_RMH'],
						TG_Win_Rate_RB: item['game']['IOR_RMC'],
						M_Flat_Rate_RB: item['game']['IOR_RMN'],
						MB_Win_Rate_RB_H: item['game']['IOR_HRMH'],
						TG_Win_Rate_RB_H: item['game']['IOR_HRMC'],
						M_Flat_Rate_RB_H: item['game']['IOR_HRMN'],
						FLAG_CLASS: item['game']['FLAG_CLASS'],
						RETIME_SET: item['game']["RETIMESET"].split("^")[0] + " " + item['game']["RETIMESET"].split("^")[1],
						// Eventid: item['game']['EVENTID'],
						Hot: item['game']['HOT'] == "Y"? 1: 0,
						Play: item['game']['PLAY'] == "Y"? 1: 0,
						MID: item['game']['GID'],
						RB_Show: 1,
						S_Show: 0,
						isSub: 1,
					};
					try {
						response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_IN_PLAY}`, data);
						// await sleep(2000);
						console.log("response==============================", response.data);
					} catch(err) {
						console.log("err===================", err.response);
					}
				}
			}
		}
	} catch(e) {
		console.log(e)
		await sleep(2000);
	}
}

exports.getFT_HDP_OU_INPLAY = async (item) => {
	try {
		let thirdPartyBaseUrl = "https://www.hga030.com";
		let thirdPartyUrl = "";
		let version = "-3ed5-newkeyboard-0303-95881ae5676be2";
		let uID = "hojnotcm27417505l93240b0";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;

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
				console.log("getGameOBT:=============", result);
				if (result["serverresponse"]["code"] != "noData" && result["serverresponse"]["ec"]["game"].length > 0) {
					for (let i = 0; i < result["serverresponse"]["ec"]["game"].length; i++) {
						if (i == 0) {
							data["RATIO_RE_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
							data["IOR_REH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
							data["IOR_REC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
							data["RATIO_ROUO_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
							data["RATIO_ROUU_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
							data["IOR_ROUH_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
							data["IOR_ROUC_HDP_0"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
						}
						if (i == 1) {
							data["RATIO_RE_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
							data["IOR_REH_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
							data["IOR_REC_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
							data["RATIO_ROUO_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
							data["RATIO_ROUU_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
							data["IOR_ROUH_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
							data["IOR_ROUC_HDP_1"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
						}
						if (i == 2) {
							data["RATIO_RE_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
							data["IOR_REH_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
							data["IOR_REC_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
							data["RATIO_ROUO_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
							data["RATIO_ROUU_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
							data["IOR_ROUH_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
							data["IOR_ROUC_HDP_2"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
						}
						if (i == 3) {
							data["RATIO_RE_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_RE"];
							data["IOR_REH_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REH"];
							data["IOR_REC_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_REC"];
							data["RATIO_ROUO_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUO"];
							data["RATIO_ROUU_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["RATIO_ROUU"];
							data["IOR_ROUH_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUH"];
							data["IOR_ROUC_HDP_3"] = result["serverresponse"]["ec"]["game"][i]["IOR_ROUC"];
						}
					}
					try {									
						response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_HDP_OBT}`, data);
						console.log("FT_HDP_OBT_RESPONSE:===============", response.data);
					} catch(err) {
						console.log("FT_HDP_OBT_ERROR:===============", err);
					}
				}
			}
		}
	} catch(e) {
		console.log(e)
		await sleep(2000);
	}
}

exports.getFT_CORNER_INPLAY = async (item) => {
	try {
		let thirdPartyBaseUrl = "https://www.hga030.com";
		let thirdPartyUrl = "";
		let version = "-3ed5-newkeyboard-0303-95881ae5676be2";
		let uID = "hojnotcm27417505l93240b0";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;

			console.log("item============", item)

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

			data["MID"] = item["id"];

			if (cnResponse.status === 200) {
				let result = parser.parse(cnResponse.data);
				if (result["serverresponse"]["code"] !== "noData") {
					console.log("getGameOBT_CN:===============", result["serverresponse"]["ec"]["game"]);
					data["RATIO_ROUO_CN"] = result["serverresponse"]["ec"]["game"]["RATIO_ROUO"];
					data["RATIO_ROUU_CN"] = result["serverresponse"]["ec"]["game"]["RATIO_ROUU"];
					data["IOR_ROUH_CN"] = result["serverresponse"]["ec"]["game"]["IOR_ROUH"];
					data["IOR_ROUC_CN"] = result["serverresponse"]["ec"]["game"]["IOR_ROUC"];
					data["RATIO_HROUO_CN"] = result["serverresponse"]["ec"]["game"]["RATIO_HROUO"];
					data["RATIO_HROUU_CN"] = result["serverresponse"]["ec"]["game"]["RATIO_HROUU"];
					data["IOR_HROUH_CN"] = result["serverresponse"]["ec"]["game"]["IOR_HROUH"];
					data["IOR_HROUC_CN"] = result["serverresponse"]["ec"]["game"]["IOR_HROUC"];
					data["STR_ODD_CN"] = result["serverresponse"]["ec"]["game"]["STR_ODD"];
					data["STR_EVEN_CN"] = result["serverresponse"]["ec"]["game"]["STR_EVEN"];
					data["IOR_REOO_CN"] = result["serverresponse"]["ec"]["game"]["IOR_REOO"];
					data["IOR_REOE_CN"] = result["serverresponse"]["ec"]["game"]["IOR_REOE"];
					data["STR_HODD_CN"] = result["serverresponse"]["ec"]["game"]["STR_HODD"];
					data["STR_HEVEN_CN"] = result["serverresponse"]["ec"]["game"]["STR_HEVEN"];
					data["IOR_HREOO_CN"] = result["serverresponse"]["ec"]["game"]["IOR_HREOO"];
					data["IOR_HREOE_CN"] = result["serverresponse"]["ec"]["game"]["IOR_HREOE"];
					data["WTYPE_CN"] = result["serverresponse"]["ec"]["game"]["WTYPE_CN"];
					data["IOR_RNCH_CN"] = result["serverresponse"]["ec"]["game"]["IOR_RNCH"];
					data["IOR_RNCC_CN"] = result["serverresponse"]["ec"]["game"]["IOR_RNCC"];
					try {									
						response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_CORNER_OBT}`, data);
						console.log("FT_CORNER_OBT_RESPONSE:===============", response.data);
					} catch(err) {
						console.log("FT_CORNER_OBT_ERROR:===============", err);
					}
				}
			}
		}
	} catch(e) {
		console.log(e)
		await sleep(2000);
	}
}

exports.getFT_CORRECT_SCORE_INPLAY = async () => {
	try {
		let thirdPartyBaseUrl = "";
		let thirdPartyUrl = "";
		let version = "";
		let uID = "";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;
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
				console.log("correctScore:====================", result);
				let totalDataCount = result['serverresponse']['totalDataCount'];
				if (totalDataCount > 1) {
					result['serverresponse']['ec'].map(async item => {
						let data = {
							Type: "FT",
							MB_Ball: item['game']['SCORE_H'],
							TG_Ball: item['game']['SCORE_C'],
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
							UP5H: item['game']['IOR_ROVH'],
							UP5: item['game']['IOR_ROVC'],
							MID: item['game']['GID'],
							PD_Show: 1,
						};
						try {
							response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_CORRECT_SCORE}`, data);
							console.log("response==============================", response.data);
						} catch(err) {
							console.log("err===================", err);
						}
					})			
				} else if(totalDataCount == 1) {
					let item = result['serverresponse']['ec'];
					let data = {
						Type: "FT",
						MB_Ball: item['game']['SCORE_H'],
						TG_Ball: item['game']['SCORE_C'],
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
						UP5H: item['game']['IOR_ROVH'],
						UP5: item['game']['IOR_ROVC'],
						MID: item['game']['GID'],
						PD_Show: 1,				
					};
					try {
						response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_CORRECT_SCORE}`, data);
						console.log("response==============================", response.data);
					} catch(err) {
						console.log("err===================", err.response);
					}
				}
			}
		}
	} catch(e) {
		console.log(e)
	}
}

function sleep (milliseconds) {
  	return new Promise((resolve) => setTimeout(resolve, milliseconds))
}

exports.getFT_FU_R_EARLY = async () => {
	try {
		let thirdPartyBaseUrl = "";
		let thirdPartyUrl = "";
		let version = "";
		let uID = "";
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			version = response.data.data.ver;
			uID = response.data.data.Uid;
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
				var lid = ""
				let result = parser.parse(response.data);
				console.log("2222222222222222222222222222", result['serverresponse']['coupons']['coupon']);
				if (result['serverresponse']['coupons']['coupon'].length > 0) {
					lid = result['serverresponse']['coupons']['coupon'][0]['lid'];
				} else {
					lid = result['serverresponse']['coupons']['coupon']['lid'];
				}
				let lid_array = lid.split(",");
				console.log(lid_array);
				lid_array.map(async lid => {
					let formData = new FormData();
					formData.append("p", "get_game_list");
					formData.append("uid", uID);
					formData.append("ver", version);
					formData.append("langx", "zh-cn");
					formData.append("p3type", "");
					formData.append("date", "all");
					formData.append("gtype", "ft");
					formData.append("showtype", "early");
					formData.append("rtype", "r");
					formData.append("ltype", 3);
					formData.append("lid", lid);
					formData.append("action", "click_league");
					formData.append("sorttype", "L");
					formData.append("specialClick", "");
					formData.append("isFantasy", "N");
					formData.append("ts", new Date().getTime());
					response = await axios.post(thirdPartyUrl, formData);
					if (response.status === 200) {
						let result = parser.parse(response.data);
						let totalDataCount = result['serverresponse']['totalDataCount'];
						if (totalDataCount > 1) {
							result['serverresponse']['ec'].map(async item => {
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
								let data = {
									Type: 'FT',
									LID: item['game']['LID'],
									MB_MID: item['game']['GNUM_H'],
									TG_MID: item['game']['GNUM_C'],
									S_Single_Rate: item['game']['IOR_EOO'],
									S_Double_Rate: item['game']['IOR_EOE'],
									Play: item['game']['PLAY'] == "Y" ? 1 : 0,
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
									ShowTypeHR: item['game']['STRONG'],
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
								};
								try {
									response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_FU_R}`, data);
									console.log("response==============================", response.data);
								} catch(err) {
									console.log("err===================", err.response);
								}
							})			
						} else if(totalDataCount == 1) {
							let item = result['serverresponse']['ec'];
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
							let data = {
								Type: 'FT',
								LID: item['game']['LID'],
								MB_MID: item['game']['GNUM_H'],
								TG_MID: item['game']['GNUM_C'],
								S_Single_Rate: item['game']['IOR_EOO'],
								S_Double_Rate: item['game']['IOR_EOE'],
								Play: item['game']['PLAY'] == "Y" ? 1 : 0,
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
								ShowTypeHR: item['game']['STRONG'],
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
							};
							try {
								response = await axios.post(`${BACKEND_BASE_URL}${MATCH_SPORTS}${SAVE_FT_FU_R}`, data);
								console.log("response==============================", response.data);
							} catch(err) {
								console.log("err===================", err.response);
							}
						}
					}
				})
			}
		}
	} catch(e) {
		console.log(e)
	}
}
