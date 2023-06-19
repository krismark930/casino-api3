const axios = require('axios');
const { BACKEND_BASE_URL, GET_WEB_SYSTEM_DATA, WEB_SYSTEM_DATA } = require('../api');
var FormData = require('form-data');
var convert = require('xml-js');
var moment = require('moment');

exports.getUID_VER = async (userName, passWord, thirdPartyBaseUrl) => {
	console.log("11111111111111111111", userName, passWord, thirdPartyBaseUrl);
	try {
		let thirdPartyUrl = "";
		let version = "-3ed5-bug4-0309-95881ae5676be2";
		let uid = "";
		let id = 0;
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
			// console.log("getUID_VER_Result: ", result);
			uid = result['serverresponse']['uid']['_text'];
		}
		return uid;
	} catch(e) {
		// console.log(e)
	}
}

exports.dispatchUID_VER = async (data) => {
	try {
		response = await axios.put(`${BACKEND_BASE_URL}${WEB_SYSTEM_DATA}/${id}`, data);
		// console.log("dispatchUID_VER: ", response.data)
	} catch(e) {
		// console.log(e);
	}
}