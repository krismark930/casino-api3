const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { SCORE } = require('../api');
const { SAVE_FT_THIRDPARTY_SCORE } = require('../api');

exports.getFTScore = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			// console.log("getFTScore: ", response);
			response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_FT_THIRDPARTY_SCORE}`, {cryptedData: response.data});
			if (response.status === 200) {
				console.log("saveFTScore: ", response.data);
			}
		}
		return null;
	} catch(e) {
		console.log(e);
		return null;
	}
}