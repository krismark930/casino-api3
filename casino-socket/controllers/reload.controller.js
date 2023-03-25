const axios = require('axios');
const { BACKEND_BASE_URL, GET_WEB_SYSTEM_DATA, WEB_SYSTEM_DATA } = require('../api');
var FormData = require('form-data');
var convert = require('xml-js');
var moment = require('moment');

exports.getUID_VER = async (userName, passWord, thirdPartyBaseUrl) => {
	try {
		let thirdPartyUrl = "";
		let version = "-3ed5-bug4-0309-95881ae5676be2";
		let uID = "";
		let id = 0;
		let data = {};
		// let response = await axios.get(`${BACKEND_BASE_URL}${GET_WEB_SYSTEM_DATA}`);
		// if (response.status == 200 && response.data.success) {
			// thirdPartyBaseUrl = response.data.data.datasite;
			// id = response.data.data.id;
			// version = response.data.data.ver;
			// username = response.data.data.UserName;
			// password = response.data.data.password;
			thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
			let formData = new FormData();
			formData.append("p", "chk_login");
			formData.append("ver", version);
			formData.append("langx", "zh-cn");
			formData.append("username", userName);
			formData.append("password", passWord);
			formData.append("app", "N");
			formData.append("auto", "GZCZHC");
			response = await axios.post(thirdPartyUrl, formData);
			if (response.status === 200) {
				var result = JSON.parse(convert.xml2json(response.data, {compact: true, spaces: 4}));
				console.log("getUID_VER_Result: ", result);
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
					return data;
				}
			}
		// }
		return data;
	} catch(e) {
		console.log(e)
	}
}

exports.dispatchUID_VER = async (data) => {
	try {
		response = await axios.put(`${BACKEND_BASE_URL}${WEB_SYSTEM_DATA}/${id}`, data);
		console.log("dispatchUID_VER: ", response.data)
	} catch(e) {
		console.log(e);
	}
}