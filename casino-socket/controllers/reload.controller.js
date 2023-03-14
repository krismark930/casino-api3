const axios = require('axios');
const { BACKEND_BASE_URL, GET_WEB_SYSTEM_DATA, WEB_SYSTEM_DATA } = require('../api');
var FormData = require('form-data');
var convert = require('xml-js');
var moment = require('moment');

exports.getUID_VER = async () => {
	try {
		let thirdPartyBaseUrl = "";
		let thirdPartyUrl = "";
		let version = "";
		let uID = "";
		let username = "";
		let password = "";
		let id = 0;
		let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		if (response.status == 200 && response.data.success) {
			thirdPartyBaseUrl = response.data.data.datasite;
			id = response.data.data.id;
			version = response.data.data.ver;
			uID = response.data.data.Uid;
			username = response.data.data.UserName;
			password = response.data.data.password;
			thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
			let formData = new FormData();
			formData.append("p", "chk_login");
			formData.append("ver", version);
			formData.append("langx", "zh-cn");
			formData.append("username", username);
			formData.append("password", password);
			formData.append("app", "N");
			formData.append("auto", "GZCZHC");
			response = await axios.post(thirdPartyUrl, formData);
			if (response.status === 200) {
				var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
				uID = result['serverresponse']['uid']['_text'];
				let formData = new FormData();
				formData.append("p", "get_version");
				formData.append("uid", uID);
				formData.append("langx", "zh-cn");
				response = await axios.post(thirdPartyUrl, formData);
				if (response.status === 200) {
					var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
					version = result['serverrequest']['ver']['_text'];
					let data = {
						version: version,
						uid: uID
					}
					try {
						response = await axios.put(`${BACKEND_BASE_URL}${WEB_SYSTEM_DATA}/${id}`, data);
						console.log("111111111111", response.data)
					} catch(e) {
						console.log(e);
					}
				}
			}
		}
	} catch(e) {
		console.log(e)
	}
}