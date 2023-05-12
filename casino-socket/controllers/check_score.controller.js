const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { FT_AUTO_CHECK_SCORE } = require('../api');
const { BK_AUTO_CHECK_SCORE } = require('../api');
const { FT_PARLAY_AUTO_CHECK_SCORE } = require('../api');
const { BK_PARLAY_AUTO_CHECK_SCORE } = require('../api');

exports.dispatchFT_AUTO_CHECK_SCORE = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${FT_AUTO_CHECK_SCORE}`, {});
		console.log("dispatchFT_AUTO_CHECK_SCORE: ", response.data);
	} catch(err) {
		console.log("dispatchFT_AUTO_CHECK_SCORE_ERROR: ", err);
	}
}

exports.dispatchBK_AUTO_CHECK_SCORE = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${BK_AUTO_CHECK_SCORE}`, {});
		console.log("dispatchBK_AUTO_CHECK_SCORE: ", response.data);
	} catch(err) {
		console.log("dispatchBK_AUTO_CHECK_SCORE_ERROR: ", err);
	}
}

exports.dispatchFT_PARLAY_AUTO_CHECK_SCORE = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${FT_PARLAY_AUTO_CHECK_SCORE}`, {});
		console.log("dispatchFT_PARLAY_AUTO_CHECK_SCORE: ", response.data);
	} catch(err) {
		console.log("dispatchFT_PARLAY_AUTO_CHECK_SCORE_ERROR: ", err);
	}
}

exports.dispatchBK_PARLAY_AUTO_CHECK_SCORE = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${BK_PARLAY_AUTO_CHECK_SCORE}`, {});
		console.log("dispatchBK_PARLAY_AUTO_CHECK_SCORE: ", response.data);
	} catch(err) {
		console.log("dispatchBK_PARLAY_AUTO_CHECK_SCORE_ERROR: ", err);
	}
}