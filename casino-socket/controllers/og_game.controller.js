const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { GET_OG_TOKEN } = require('../api');
const { GET_KY_TRANSACTION } = require('../api');

exports.dispatchGetOGToken = async (url) => {
	try {
		let response = await axios.post(`${BACKEND_BASE_URL}${GET_OG_TOKEN}`, {});
		console.log("dispatchGetOGToken: ", response.data);
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetOGTransaction = async (url) => {
	try {
		let response = await axios.post(`${BACKEND_BASE_URL}${GET_OG_TRANSACTION}`, {});
		console.log("dispatchGetOGTranaction: ", response.data);
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetKYTransaction = async (url) => {
	try {
		let response = await axios.post(`${BACKEND_BASE_URL}${GET_KY_TRANSACTION}`, {});
		console.log("dispatchGetKYTransaction: ", response.data);
	} catch(e) {
		console.log(e);
	}
}