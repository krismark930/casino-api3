const axios = require('axios');
const { BACKEND_BASE_URL } = require('../api');
const { LOTTERY_RESULT } = require('../api');
const { LOTTERY_RESULT_CQSSC } = require('../api');
const { LOTTERY_RESULT_FFC5 } = require('../api');
const { LOTTERY_RESULT_TXSSC } = require('../api');
const { LOTTERY_RESULT_TWSSC } = require('../api');
const { LOTTERY_RESULT_AZXY5 } = require('../api');
const { LOTTERY_RESULT_XJSSC } = require('../api');
const { LOTTERY_RESULT_TJSSC } = require('../api');
const { LOTTERY_RESULT_GD11 } = require('../api');
const { LOTTERY_RESULT_AZXY10 } = require('../api');
const { LOTTERY_RESULT_BJPK } = require('../api');
const { LOTTERY_RESULT_XYFT } = require('../api');
const { LOTTERY_RESULT_CQSF } = require('../api');
const { LOTTERY_RESULT_TJSF } = require('../api');
const { LOTTERY_RESULT_GDSF } = require('../api');
const { LOTTERY_RESULT_GXSF } = require('../api');
const { LOTTERY_RESULT_SHSSL } = require('../api');
const { LOTTERY_RESULT_TCPL3 } = require('../api');
const { LOTTERY_RESULT_FC3D } = require('../api');
const { CHECKOUT_AZXY10 } = require('../api');
const { CHECKOUT_AZXY5 } = require('../api');
const { CHECKOUT_BJKN } = require('../api');
const { CHECKOUT_BJPK } = require('../api');
const { CHECKOUT_CQ } = require('../api');
const { CHECKOUT_CQSF } = require('../api');
const { CHECKOUT_D3 } = require('../api');
const { CHECKOUT_FFC5 } = require('../api');
const { CHECKOUT_GD11 } = require('../api');
const { CHECKOUT_GDSF } = require('../api');
const { CHECKOUT_GXSF } = require('../api');
const { CHECKOUT_JX } = require('../api');
const { CHECKOUT_P3 } = require('../api');
const { CHECKOUT_T3 } = require('../api');
const { CHECKOUT_TJ } = require('../api');
const { CHECKOUT_TJSF } = require('../api');
const { CHECKOUT_TWSSC } = require('../api');
const { CHECKOUT_TXSSC } = require('../api');
const { CHECKOUT_XYFT } = require('../api');

exports.dispatchGetLotteryResult = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT}`, {cryptedData: response.data});
			console.log("dispatchGetLotteryResult: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultCQSSC = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_CQSSC}`, {data: response.data});
			console.log("dispatchGetLotteryResultCQSSC: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultFFC5 = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_FFC5}`, {data: response.data});
			console.log("dispatchGetLotteryResultFFC5: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultTXSSC = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_TXSSC}`, {data: response.data});
			console.log("dispatchGetLotteryResultTXSSC: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultTWSSC = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_TWSSC}`, {data: response.data});
			console.log("dispatchGetLotteryResultTWSSC: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultAZXY5 = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_AZXY5}`, {data: response.data});
			console.log("dispatchGetLotteryResultAZXY5: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultXJSSC = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_XJSSC}`, {data: response.data});
			console.log("dispatchGetLotteryResultXJSSC: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultTJSSC = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_TJSSC}`, {data: response.data});
			console.log("dispatchGetLotteryResultTJSSC: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultGD11 = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_GD11}`, {data: response.data});
			console.log("dispatchGetLotteryResultGD11: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultAZXY10 = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_AZXY10}`, {data: response.data});
			console.log("dispatchGetLotteryResultAZXY10: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultBJPK = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_BJPK}`, {data: response.data});
			console.log("dispatchGetLotteryResultBJPK: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultXYFT = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_XYFT}`, {data: response.data});
			console.log("dispatchGetLotteryResultXYFT: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultCQSF = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_CQSF}`, {data: response.data});
			console.log("dispatchGetLotteryResultCQSF: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultGDSF = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_GDSF}`, {data: response.data});
			console.log("dispatchGetLotteryResultGDSF: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultGXSF = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_GXSF}`, {data: response.data});
			console.log("dispatchGetLotteryResultGXSF: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultTJSF = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_TJSF}`, {data: response.data});
			console.log("dispatchGetLotteryResultTJSF: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultSHSSL = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_SHSSL}`, {data: response.data});
			console.log("dispatchGetLotteryResultTCPL3: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultTCPL3 = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_TCPL3}`, {data: response.data});
			console.log("dispatchGetLotteryResultTCPL3: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchGetLotteryResultFC3D = async (url) => {
	let response;
	try {
		response = await axios.get(url);
		if (response.status === 200) {
			response = await axios.post(`${BACKEND_BASE_URL}${LOTTERY_RESULT_FC3D}`, {data: response.data});
			console.log("dispatchGetLotteryResultFC3D: ", response.data);
		}
	} catch(e) {
		console.log(e);
	}
}

exports.dispatchCheckoutAZXY10 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_AZXY10}`, {});
		console.log("dispatchCheckoutAZXY10: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutAZXY10_ERROR: ", err);
	}
}

exports.dispatchCheckoutAZXY5 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_AZXY5}`, {});
		console.log("dispatchCheckoutAZXY5: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutAZXY5_ERROR: ", err);
	}
}

exports.dispatchCheckoutBJKN = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_BJKN}`, {});
		console.log("dispatchCheckoutBJKN: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutBJKN_ERROR: ", err);
	}
}

exports.dispatchCheckoutBJPK = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_BJPK}`, {});
		console.log("dispatchCheckoutBJPK: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutBJPK_ERROR: ", err);
	}
}

exports.dispatchCheckoutCQ = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_CQ}`, {});
		console.log("dispatchCheckoutCQ: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutCQ_ERROR: ", err);
	}
}

exports.dispatchCheckoutCQSF = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_CQSF}`, {});
		console.log("dispatchCheckoutCQSF: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutCQSF_ERROR: ", err);
	}
}

exports.dispatchCheckoutD3 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_D3}`, {});
		console.log("dispatchCheckoutD3: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutD3_ERROR: ", err);
	}
}

exports.dispatchCheckoutFFC5 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_FFC5}`, {});
		console.log("dispatchCheckoutFFC5: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutFFC5_ERROR: ", err);
	}
}

exports.dispatchCheckoutGD11 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_GD11}`, {});
		console.log("dispatchCheckoutGD11: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutGD11_ERROR: ", err);
	}
}

exports.dispatchCheckoutGDSF = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_GDSF}`, {});
		console.log("dispatchCheckoutGDSF: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutGDSF_ERROR: ", err);
	}
}

exports.dispatchCheckoutGXSF = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_GXSF}`, {});
		console.log("dispatchCheckoutGXSF: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutGXSF_ERROR: ", err);
	}
}

exports.dispatchCheckoutJX = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_JX}`, {});
		console.log("dispatchCheckoutJX: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutJX_ERROR: ", err);
	}
}

exports.dispatchCheckoutP3 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_P3}`, {});
		console.log("dispatchCheckoutP3: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutP3_ERROR: ", err);
	}
}

exports.dispatchCheckoutT3 = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_T3}`, {});
		console.log("dispatchCheckoutT3: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutT3_ERROR: ", err);
	}
}

exports.dispatchCheckoutTJ = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_TJ}`, {});
		console.log("dispatchCheckoutTJ: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutTJ_ERROR: ", err);
	}
}

exports.dispatchCheckoutTJSF = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_TJSF}`, {});
		console.log("dispatchCheckoutTJSF: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutTJSF_ERROR: ", err);
	}
}

exports.dispatchCheckoutTWSSC = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_TWSSC}`, {});
		console.log("dispatchCheckoutTWSSC: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutTWSSC_ERROR: ", err);
	}
}

exports.dispatchCheckoutTXSSC = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_TXSSC}`, {});
		console.log("dispatchCheckoutTXSSC: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutTXSSC_ERROR: ", err);
	}
}

exports.dispatchCheckoutXYFT = async () => {
	try {
		response = await axios.post(`${BACKEND_BASE_URL}${CHECKOUT_XYFT}`, {});
		console.log("dispatchCheckoutXYFT: ", response.data);
	} catch(err) {
		console.log("dispatchCheckoutXYFT_ERROR: ", err);
	}
}