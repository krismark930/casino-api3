const axios = require('axios');
const { BACKEND_BASE_URL, GET_WEB_SYSTEM_DATA, MATCH_SPORTS, SAVE_FT_FU_R, MATCH_CROWN, SAVE_FT_IN_PLAY } = require('../api');
var FormData = require('form-data');
var convert = require('xml-js');
const { XMLParser, XMLBuilder, XMLValidator} = require("fast-xml-parser");
var moment = require('moment');
const parser = new XMLParser();

exports.getLeagueCount = async () => {
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
			formData.append("p", "get_league_count");
			formData.append("uid", uID);
			formData.append("ver", version);
			formData.append("langx", "zh-cn");
			formData.append("sorttype", "league");
			formData.append("date", "ALL");
			formData.append("ltype", 3);
			formData.append("ts", new Date().getTime());
			response = await axios.post(thirdPartyUrl, formData);
			if (response.status === 200) {
				var result = parser.parse(response.data);
				// var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
				console.log(result['serverresponse']['game']);
			}
		}
	} catch(e) {
		console.log(e)
	}
}
