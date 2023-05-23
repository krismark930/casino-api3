const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { GET_LOTTERY_STATUS } = require('../api');
const { UPDATE_HANDICAP } = require('../api');
const { GET_MACAO_LOTTERY_STATUS } = require('../api');
const { UPDATE_MACAO_HANDICAP } = require('../api');

exports.dispatchGetLotteryStatus = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${GET_LOTTERY_STATUS}`, {});
		// console.log("dispatchGetLotteryStatus: ", response.data.data);
		if (response.status == 200) {
			return response.data.data;
		} else {
			return null;
		}
	} catch(err) {
		console.log("dispatchGetLotteryStatusError: ", err);
		return null;
	}
}

exports.dispatchUpdateLotteryHandicap = async (data) => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${UPDATE_HANDICAP}`, data);
		console.log("updateLotteryHandicap: ", response.data);
	} catch(err) {
		console.log("updateLotteryHandicap_ERROR: ", err);
	}
}

exports.dispatchGetMacaoLotteryStatus = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${GET_MACAO_LOTTERY_STATUS}`, {});
		// console.log("dispatchGetMacaoLotteryStatus: ", response.data.data);
		if (response.status == 200) {
			return response.data.data;
		} else {
			return null;
		}
	} catch(err) {
		console.log("dispatchGetMacaoLotteryStatusError: ", err);
		return null;
	}
}

exports.dispatchUpdateMacaoLotteryHandicap = async (data) => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${UPDATE_MACAO_HANDICAP}`, data);
		console.log("updateMacaoLotteryHandicap: ", response.data);
	} catch(err) {
		console.log("updateMacaoLotteryHandicap_ERROR: ", err);
	}
}