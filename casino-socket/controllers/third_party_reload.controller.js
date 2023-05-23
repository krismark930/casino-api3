const axios = require('axios');

var FormData = require('form-data');

const { XMLParser } = require("fast-xml-parser");

const options = {
    ignoreAttributes : false,
    attributeNamePrefix : ""
};

const parser = new XMLParser(options);

exports.getSystemTime = async (thirdPartyAuthData) => {
    try {
        let thirdPartyBaseUrl = thirdPartyAuthData["thirdPartyBaseUrl"];
        let thirdPartyUrl = "";
        let version = thirdPartyAuthData["version"];
        let uID = thirdPartyAuthData["uid"];
        thirdPartyUrl = `${thirdPartyBaseUrl}/transform.php?ver=${version}`;
        let formData = new FormData();
        formData.append("p", "get_systemTime");
        formData.append("uid", uID);
        formData.append("ver", version);
        formData.append("langx", "zh-cn");
        response = await axios.post(thirdPartyUrl, formData);
        // return response;
        if (response.status === 200) {
            var result = parser.parse(response.data);
            return result;
        } else {
            return false;
        }
    } catch(e) {
        console.log(e)
        return false;
    }
}
