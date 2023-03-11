const axios = require('axios');
const { BACKEND_BASE_URL, GET_WEB_SYSTEM_DATA } = require('../api');
var FormData = require('form-data');
var convert = require('xml-js');

exports.getSeverUrl = async () => {
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
			formData.append("p", "service_mainget");
			formData.append("ver", version);
			formData.append("langx", "zh-cn");
			formData.append("login", "N");
			response = await axios.post(thirdPartyUrl, formData);
			if (response.status === 200) {
				var result = convert.xml2json(response.data, {compact: true, spaces: 4});
			}
		}
	} catch(e) {
		console.log(e)
	}
}