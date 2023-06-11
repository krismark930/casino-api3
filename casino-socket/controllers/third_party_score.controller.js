const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { SCORE } = require('../api');
const { SAVE_FT_THIRDPARTY_SCORE } = require('../api');
const { SAVE_BK_THIRDPARTY_SCORE } = require('../api');
const { SAVE_OTHER_SCORE } = require('../api');

exports.getFTScore = async (url) => {
	let response;
	try {
		response = await axios.get(`${url}FT`);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_FT_THIRDPARTY_SCORE}`, {cryptedData: response.data});
			console.log("getFTScore: ", response);
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

exports.getBKScore = async (url) => {
	let response;
	try {
		response = await axios.get(`${url}BK`);
		if (response.status === 200) {
			// console.log("getFTScore: ", response);
			response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_BK_THIRDPARTY_SCORE}`, {cryptedData: response.data});
			if (response.status === 200) {
				// console.log("saveBKScore: ", response.data);
			}
		}
		return null;
	} catch(e) {
		console.log(e);
		return null;
	}
}

exports.getOtherFTScore = async (data) => {
	try {
		let response;
		response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_OTHER_SCORE}`, {uid: data.uid, game_type: "FT", date: "Today"});
		response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_OTHER_SCORE}`, {uid: data.uid, game_type: "FT", date: "Yesterday"});
		if (response.status === 200) {
			console.log("saveFTOtherScore: ", response.data);
		}
		return null;
	} catch(e) {
		console.log(e);
		return null;
	}
}

exports.getOtherBKScore = async (data) => {
	try {
		let response;
		response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_OTHER_SCORE}`, {uid: data.uid, game_type: "BK", date: "Today"});
		response = await axios.post(`${BACKEND_BASE_URL}${SCORE}${SAVE_OTHER_SCORE}`, {uid: data.uid, game_type: "BK", date: "Yesterday"});
		if (response.status === 200) {
			// console.log("saveBKOtherScore: ", response.data);
		}
		return null;
	} catch(e) {
		console.log(e);
		return null;
	}
}