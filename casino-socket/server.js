const app = require('express')();
const server = require('http').createServer(app);
const io = require('socket.io')(server);
const axios = require('axios');
var fs = require( 'fs' );
const { BACKEND_BASE_URL } = require('./api');
const { MATCH_SPORTS } = require('./api');
const { GET_FT_DATA} = require('./api');
const { GET_IN_PLAY_DATA } = require('./api');
const { GET_IN_PLAY_SCORE } = require('./api');
var { getLeagueCount } = require('./controllers/third_party_league.controller');
var { getUID_VER } = require('./controllers/reload.controller');
var { dispatchUID_VER } = require('./controllers/reload.controller');
var { getSeverUrl } = require('./controllers/serverUrl.controller');
var { getFT_FU_R_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_FU_R_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_FU_R_EARLY } = require('./controllers/third_party_ft.controller');
var { getFT_PD } = require('./controllers/third_party_ft.controller');
var { getFT_FS } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORNER_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_CORRECT_SCORE_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_FU_R_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_HDP_OU_INPLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_CORNER_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_DEFAULT_TODAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_DEFAULT_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORNER_TODAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_CORNER_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_EARLY } = require('./controllers/third_party_ft.controller');
var { getFT_DEFAULT_EARLY } = require('./controllers/third_party_ft.controller');

var obtIterval = 0;
var correctScoreInterval = 0;
var ftInPlayInterval = 0;
var receivedFTInPlayInterval = 0;
var receivedFTScoreInterval = 0;
var leagueTodayInterval = 0;
var leagueEarlyInterval = 0;
var ftTodayInterval = 0;

const userName = "4060hg";
const passWord = "yy667788"
const thirdPartyBaseUrl = "https://www.hga030.com"
var thirdPartyAuthData = {    
    uid: "ygzq63asm27417503l225064b0",
    version: "-3ed5-bug4-0309-95881ae5676be2",
    thirdPartyBaseUrl: "https://www.hga030.com"
}

// setInterval(async () => {
//     let data = await getUID_VER(userName, passWord, thirdPartyBaseUrl);
//     console.log(data);
//     thirdPartyAuthData["uid"] = data["uid"];
//     thirdPartyAuthData["version"] = data["version"];
//     if (data != {}) {
//         // await dispatchUID_VER(data);
//     }
// }, 3600000);

// setTimeout(async () => {
//     let data = await getUID_VER(userName, passWord, thirdPartyBaseUrl);
//     console.log(data);
//     thirdPartyAuthData["uid"] = data["uid"];
//     thirdPartyAuthData["version"] = data["version"];
//     if (data != {}) {
//         // await dispatchUID_VER(data);
//     }
// }, 2000);

io.on("connection", async function (socket) {

    console.log("socket connected:" + socket.id);

    socket.on('sendHDP_OUData', async data => {
        console.log(data);
        clearInterval(obtIterval);
        let item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
        console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_Data", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_Data", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 10000);
    });

    socket.on('sendCornerData', async data => {
        console.log(data);
        clearInterval(obtIterval);
        let item = await getFT_CORNER_INPLAY(thirdPartyAuthData, data);
        if (item) {
            io.emit("receivedFT_CORNER_Data", item);
            dispatchFT_CORNER_INPLAY(item);
        }
        obtIterval = setInterval( async () => {
            let item = await getFT_CORNER_INPLAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_CORNER_Data", item);
                dispatchFT_CORNER_INPLAY(item);
            }
        }, 8000)
    });

    socket.on('sendCorrectScoreMessage', async () => {
        console.log("sendCorrectScoreMessage");
        clearInterval(receivedFTInPlayInterval);
        clearInterval(ftInPlayInterval);
        clearInterval(obtIterval);
        let itemList = await getFT_CORRECT_SCORE_INPLAY(thirdPartyAuthData);
        io.emit("receivedFTInPlayScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_INPLAY(thirdPartyAuthData);
            io.emit("receivedFTInPlayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on('sendFTInPlayMessage', async () => {
        console.log("sendFTInPlayMessage");
        clearInterval(correctScoreInterval);
        clearInterval(receivedFTScoreInterval);
        let itemList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
        console.log(itemList)
        io.emit("receivedFTInPlayData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_FU_R_INPLAY(itemList)            
        }
        ftInPlayInterval = setInterval(async () => {
            let itemList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
            io.emit("receivedFTInPlayData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_FU_R_INPLAY(itemList)            
            }
        }, 60000);
    });

    socket.on("stopFT_INPLAY", () => {
        clearInterval(receivedFTInPlayInterval);
        clearInterval(ftInPlayInterval);
        clearInterval(correctScoreInterval);
        clearInterval(receivedFTScoreInterval);        
        clearInterval(obtIterval);
    })

    socket.on("sendLeagueTodayMessage", async () => {
        console.log("sendLeagueTodayMessage");
        let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
        io.emit("receivedLeagueTodayMessage", result)
        leagueTodayInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
            io.emit("receivedLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("sendLeagueEarlyMessage", async () => {
        console.log("sendLeagueEarlyMessage");
        clearInterval(leagueTodayInterval);
        let result = await getFT_LEAGUE_EARLY(thirdPartyAuthData);
        io.emit("receivedLeagueTodayMessage", result)
        leagueEarlyInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_EARLY(thirdPartyAuthData);
            io.emit("receivedLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueEarlyMessage", () => {
        clearInterval(leagueEarlyInterval);
    })

    socket.on("sendFTTodayMessage", async (data) => {
        clearInterval(leagueTodayInterval);
        let itemList = await getFT_DEFAULT_TODAY(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedFTTodayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_TODAY(itemList)            
        }
        ftTodayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_TODAY(thirdPartyAuthData, data);
            io.emit("receivedFTTodayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_TODAY(itemList)            
            }
        }, 300000);
    })

    socket.on("sendFTEarlyMessage", async (data) => {
        clearInterval(leagueTodayInterval);
        let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedFTTodayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_TODAY(itemList)            
        }
        ftTodayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, data);
            io.emit("receivedFTTodayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_TODAY(itemList)            
            }
        }, 300000);
    })

    socket.on('sendHDP_OU_TODAY', async data => {
        console.log(data);
        clearInterval(obtIterval);
        let item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
        console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_TODAY", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_TODAY", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 10000);
    });

    socket.on('sendCornerToday', async data => {
        console.log(data);
        clearInterval(obtIterval);
        let item = await getFT_CORNER_TODAY(thirdPartyAuthData, data);
        if (item) {
            io.emit("receivedFT_CORNER_TODAY", item);
            // dispatchFT_CORNER_TODAY(item);
        }
        obtIterval = setInterval( async () => {
            let item = await getFT_CORNER_TODAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_CORNER_TODAY", item);
                // dispatchFT_CORNER_TODAY(item);
            }
        }, 8000)
    });

    socket.on('sendCorrectScoreToday', async (data) => {
        clearInterval(leagueTodayInterval);
        clearInterval(ftTodayInterval);
        clearInterval(obtIterval);
        let itemList = await getFT_CORRECT_SCORE_TODAY(thirdPartyAuthData, data);
        io.emit("receivedFTTodayScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_TODAY(thirdPartyAuthData);
            io.emit("receivedFTTodayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

});

server.listen(3000, () => {
    console.log('listening on *:3000');
});;