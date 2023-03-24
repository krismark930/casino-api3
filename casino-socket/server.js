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
var { getFT_FU_R_INPLAY } = require('./controllers/third_party_ft.controller');
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
var { getFT_CORRECT_SCORE_EARLY }  = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_CHAMPION } = require('./controllers/third_party_ft.controller');
var { getFT_MAIN_CHAMPION } = require('./controllers/third_party_ft.controller');
var { dispatchFT_MAIN_CHAMPION } = require('./controllers/third_party_ft.controller');
var { getFT_LEAGUE_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_DEFAULT_PARLAY } = require('./controllers/third_party_ft.controller');
var { dispatchFT_DEFAULT_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_PARLAY } = require('./controllers/third_party_ft.controller');
var { getFT_MAIN_FAVORITE } = require('./controllers/third_party_ft.controller');
var { dispatchFT_MAIN_FAVORITE } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_FAVORITE } = require('./controllers/third_party_ft.controller');
var { getFTScore } = require('./controllers/third_party_score.controller');
var { getBK_MAIN_INPLAY } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_INPLAY } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_TODAY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_TODAY } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_TODAY } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_EARLY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_EARLY } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_CHAMPION } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_CHAMPION } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_CHAMPION } = require('./controllers/third_party_bk.controller');
var { getBK_LEAGUE_PARLAY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_PARLAY } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_PARLAY } = require('./controllers/third_party_bk.controller');
var { getBK_MAIN_FAVORITE } = require('./controllers/third_party_bk.controller');
var { dispatchBK_MAIN_FAVORITE} = require('./controllers/third_party_bk.controller');

var obtIterval = 0;
var correctScoreInterval = 0;
var ftInPlayInterval = 0;
var leagueTodayInterval = 0;
var leagueEarlyInterval = 0;
var ftTodayInterval = 0;
var ftEarlyInterval = 0;
var leagueChampionInterval = 0;
var ftChampionInterval = 0;
var leagueParlayInterval = 0;
var ftParlayInterval = 0;
var ftFavoriteInterval = 0;
var scoreResultInterval = 0;
var uidInterval = 0;
var bkInplayInterval = 0;
var leagueBKTodayInterval = 0;
var bkTodayInterval = 0;
var bkEarlyInterval = 0;
var leagueBKEarlyInterval = 0;
var leagueBKChampionInterval = 0;
var bkChampionInterval = 0;
var leagueBKParlayInterval = 0;
var bkParlayInterval = 0;
var bkFavoriteInterval = 0;

var ftInPlayList = null;
var bkInPlayList = null;

const userName = "4060hg";
const passWord = "admin123"
const thirdPartyBaseUrl = "https://www.hga030.com";

var thirdPartyAuthData = {
    uid: "0rgd118rxcm27417505l297039b0",
    version: "-3ed5-bug4-0309-95881ae5676be2",
    thirdPartyBaseUrl: "https://www.hga030.com"
}

const thirdPartyScoreBaseUrl = ['http://zq0666.com','http://www.zq0666.com'];
const thirdPartyScoreUrl = thirdPartyScoreBaseUrl[0] + '/app/member/score.php?type=FT';

uidInterval = setInterval(async () => {
    let data = await getUID_VER(userName, passWord, thirdPartyBaseUrl);
    console.log("getUID_VER: ", data);
    if (data != undefined && data != null) {
        thirdPartyAuthData["uid"] = data["uid"];        
    }
    ftInPlayList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
    bkInPlayList = await getBK_MAIN_INPLAY(thirdPartyAuthData);
    if (data != {}) {
        // await dispatchUID_VER(data);
    }
}, 1800000);

setTimeout(async () => {
    let data = await getUID_VER(userName, passWord, thirdPartyBaseUrl);
    console.log("getUID_VER: ", data);
    if (data != undefined && data != null) {
        thirdPartyAuthData["uid"] = data["uid"];        
    }
    ftInPlayList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
    bkInPlayList = await getBK_MAIN_INPLAY(thirdPartyAuthData);
    if (data != {}) {
        // await dispatchUID_VER(data);
    }
}, 1000);

ftInPlayInterval = setInterval(async () => {
    ftInPlayList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
    io.emit("receivedFTInPlayData", ftInPlayList);
    if (ftInPlayList && ftInPlayList.length > 0) {
        dispatchFT_FU_R_INPLAY(ftInPlayList)            
    }
}, 60000);

bkInplayInterval = setInterval(async () => {
    bkInPlayList = await getBK_MAIN_INPLAY(thirdPartyAuthData);
    io.emit("receivedBKInPlayData", bkInPlayList);
    if (bkInPlayList && bkInPlayList.length > 0) {
        dispatchBK_MAIN_INPLAY(bkInPlayList)            
    }
}, 30000);

scoreResultInterval = setInterval( async () => {
    getFTScore(thirdPartyScoreUrl);
}, 10000);


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
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
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

    socket.on("stopCorrectScoreMessage", () => {
        clearInterval(correctScoreInterval);
    })

    // ========================= FT Inplay ========================= //

    socket.on('sendFTInPlayMessage', async () => {
        // console.log("sendFTInPlayMessage");
        // clearInterval(ftInPlayInterval);
        // let itemList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
        // console.log(itemList)
        // io.emit("receivedFTInPlayData", itemList);
        // if (itemList && itemList.length > 0) {
        //     dispatchFT_FU_R_INPLAY(itemList)            
        // }
        // ftInPlayInterval = setInterval(async () => {
        //     let itemList = await getFT_FU_R_INPLAY(thirdPartyAuthData);
        //     io.emit("receivedFTInPlayData", itemList);
        //     if (itemList && itemList.length > 0) {
        //         dispatchFT_FU_R_INPLAY(itemList)            
        //     }
        // }, 60000);
        io.emit("receivedFTInPlayData", ftInPlayList);
    });

    socket.on("stopFT_INPLAY", () => {
        // clearInterval(ftInPlayInterval);
        clearInterval(correctScoreInterval);        
        clearInterval(obtIterval);
    })

    //=============== Today League Data ======================//

    socket.on("sendLeagueTodayMessage", async () => {
        console.log("sendLeagueTodayMessage");
        let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
        io.emit("receivedLeagueTodayMessage", result)
        leagueTodayInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_TODAY(thirdPartyAuthData);
            io.emit("receivedLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueTodayMessage", () => {
        clearInterval(leagueTodayInterval);
    })

    //=============== Early League Data ======================//

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

    //=============== Champion League Data ======================//

    socket.on("sendLeagueChampionMessage", async () => {
        console.log("sendLeagueChampionMessage");
        let result = await getFT_LEAGUE_CHAMPION(thirdPartyAuthData);
        io.emit("receivedLeagueChampionMessage", result)
        leagueChampionInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_CHAMPION(thirdPartyAuthData);
            io.emit("receivedLeagueChampionMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueChampionMessage", () => {
        clearInterval(leagueChampionInterval);
    })

    //=============== Parlay League Data ======================//

    socket.on("sendLeagueParlayMessage", async () => {
        console.log("sendLeagueParlayMessage");
        let result = await getFT_LEAGUE_PARLAY(thirdPartyAuthData);
        io.emit("receivedLeagueParlayMessage", result)
        leagueParlayInterval = setInterval( async () => {
            let result = await getFT_LEAGUE_PARLAY(thirdPartyAuthData);
            io.emit("receivedLeagueParlayMessage", result)
        }, 300000)
    })

    socket.on("stopLeagueParlayMessage", () => {
        clearInterval(leagueParlayInterval);
    })

    //=============== FT Today Data ======================//

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

    socket.on("stopFTTodayMessage", () => {
        clearInterval(leagueTodayInterval);
        clearInterval(ftTodayInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Early Data ======================//

    socket.on("sendFTEarlyMessage", async (data) => {
        let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedFTTodayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_TODAY(itemList)            
        }
        ftEarlyInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_EARLY(thirdPartyAuthData, data);
            io.emit("receivedFTTodayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_TODAY(itemList)            
            }
        }, 300000);
    })

    socket.on("stopFTEarlyMessage", () => {
        clearInterval(leagueEarlyInterval);
        clearInterval(ftEarlyInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Champion Data ======================//

    socket.on("sendChampionMainMessage", async (data) => {
        let itemList = await getFT_MAIN_CHAMPION(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedFTChampionMessage", itemList);
        if (Array.isArray(itemList) && itemList.length > 0) {
            dispatchFT_MAIN_CHAMPION(itemList) 
        } else if (itemList) {
            let tempItemList = [];
            tempItemList.push(itemList);
            dispatchFT_MAIN_CHAMPION(tempItemList)    
        }
        ftChampionInterval = setInterval(async () => {
            let itemList = await getFT_MAIN_CHAMPION(thirdPartyAuthData, data);
            io.emit("receivedFTChampionMessage", itemList);
            if (Array.isArray(itemList) && itemList.length > 0) {
                dispatchFT_MAIN_CHAMPION(itemList) 
            } else if (itemList) {
                let tempItemList = [];
                tempItemList.push(itemList);
                dispatchFT_MAIN_CHAMPION(tempItemList)    
            }
        }, 300000);
    })

    socket.on("stopFTChampionMessage", () => {
        clearInterval(ftChampionInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Parlay Data ======================//

    socket.on("sendFTParlayMessage", async (data) => {
        let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedFTParlayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_DEFAULT_PARLAY(itemList)            
        }
        ftParlayInterval = setInterval(async () => {
            let itemList = await getFT_DEFAULT_PARLAY(thirdPartyAuthData, data);
            io.emit("receivedFTParlayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_DEFAULT_PARLAY(itemList)            
            }
        }, 300000);
    })

    socket.on("stopFTParlayMessage", () => {
        clearInterval(leagueParlayInterval);
        clearInterval(ftParlayInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Favorite Data ======================//

    socket.on("sendFTFavoriteMessage", async (data) => {
        console.log(data);
        clearInterval(ftFavoriteInterval);
        let itemList = await getFT_MAIN_FAVORITE(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedFTFavoriteMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_MAIN_FAVORITE(itemList)            
        }
        ftFavoriteInterval = setInterval(async () => {
            let itemList = await getFT_MAIN_FAVORITE(thirdPartyAuthData, data);
            io.emit("receivedFTFavoriteMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_MAIN_FAVORITE(itemList)            
            }
        }, 300000);
    })

    socket.on("stopFTFavoriteMessage", () => {
        clearInterval(leagueParlayInterval);
        clearInterval(ftParlayInterval);
        clearInterval(obtIterval);
    })

    //=============== FT Today HDP_OU Data ======================//

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

    //=============== FT Parlay HDP_OU Data ======================//

    socket.on('sendHDP_OU_PARLAY', async data => {
        console.log(data);
        clearInterval(obtIterval);
        let item  = await getFT_HDP_OU_PARLAY(thirdPartyAuthData, data);
        console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_PARLAY", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item  = await getFT_HDP_OU_PARLAY(thirdPartyAuthData, data);
            if (item) {
                io.emit("receivedFT_HDP_OU_PARLAY", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 10000);
    });

    //=============== FT Favorite HDP_OU Data ======================//

    socket.on('sendHDP_OU_FAVORITE', async data => {
        console.log(data);
        clearInterval(obtIterval);
        let item;
        if (data["showtype"] === "rb") {
            item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
        }else {
            item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
        }
        console.log("item=======", item);
        if (item) {
            io.emit("receivedFT_HDP_OU_FAVORITE", item);
            dispatchFT_HDP_OU_INPLAY(item)   
        }
        obtIterval = setInterval( async () => {
            let item;
            if (data["showtype"] === "rb") {
                item  = await getFT_HDP_OU_INPLAY(thirdPartyAuthData, data);
            }else {
                item  = await getFT_HDP_OU_TODAY(thirdPartyAuthData, data);
            }
            if (item) {
                io.emit("receivedFT_HDP_OU_FAVORITE", item);
                dispatchFT_HDP_OU_INPLAY(item)   
            }
        }, 10000);
    });

    //=============== FT Today Corner Data ======================//

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

    //=============== FT Today Correct Score Data ======================//

    socket.on('sendCorrectScoreToday', async (data) => {
        clearInterval(leagueTodayInterval);
        clearInterval(ftTodayInterval);
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_TODAY(thirdPartyAuthData, data);
        io.emit("receivedFTTodayScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_TODAY(thirdPartyAuthData, data);
            io.emit("receivedFTTodayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreToday", () => {
        clearInterval(correctScoreInterval);
    })

    //=============== FT Early Correct Score Data ======================//

    socket.on('sendCorrectScoreEarly', async (data) => {
        clearInterval(leagueEarlyInterval);
        clearInterval(ftEarlyInterval);
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_EARLY(thirdPartyAuthData, data);
        io.emit("receivedFTEarlyScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_EARLY(thirdPartyAuthData, data);
            io.emit("receivedFTEarlyScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreEarly", () => {
        clearInterval(correctScoreInterval);
    })


    //=============== FT Parlay Correct Score Data ======================//

    socket.on('sendCorrectScoreParlay', async (data) => {
        console.log('sendCorrectScoreParlay');
        clearInterval(leagueParlayInterval);
        clearInterval(ftParlayInterval);
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_PARLAY(thirdPartyAuthData, data);
        io.emit("receivedFTParlayScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_PARLAY(thirdPartyAuthData, data);
            io.emit("receivedFTParlayScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreParlay", () => {
        clearInterval(correctScoreInterval);
    })

    //=============== FT Favorite Correct Score Data ======================//

    socket.on('sendCorrectScoreFavorite', async (data) => {
        clearInterval(obtIterval);
        clearInterval(correctScoreInterval);
        let itemList = await getFT_CORRECT_SCORE_FAVORITE(thirdPartyAuthData, data);
        io.emit("receivedFTFavoriteScoreData", itemList);
        if (itemList && itemList.length > 0) {
            dispatchFT_CORRECT_SCORE_INPLAY(itemList);
        }
        correctScoreInterval = setInterval( async () => {
            let itemList = await getFT_CORRECT_SCORE_FAVORITE(thirdPartyAuthData, data);
            io.emit("receivedFTFavoriteScoreData", itemList);
            if (itemList && itemList.length > 0) {
                dispatchFT_CORRECT_SCORE_INPLAY(itemList);
            }
        }, 30000)
    });

    socket.on("stopCorrectScoreFavorite", () => {
        clearInterval(correctScoreInterval);
    })

    // ========================= BK Inplay ========================= //

    socket.on('sendBKInPlayMessage', () => {
        console.log(bkInPlayList);
        io.emit("receivedBKInPlayData", bkInPlayList);
    });

    //=============== BK Today League Data ======================//

    socket.on("sendBKLeagueTodayMessage", async () => {
        console.log("sendBKLeagueTodayMessage");
        let result = await getBK_LEAGUE_TODAY(thirdPartyAuthData);
        io.emit("receivedBKLeagueTodayMessage", result)
        leagueBKTodayInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_TODAY(thirdPartyAuthData);
            io.emit("receivedBKLeagueTodayMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueTodayMessage", () => {
        clearInterval(leagueBKTodayInterval);
    })

    //=============== BK Today Data ======================//

    socket.on("sendBKTodayMessage", async (data) => {
        let itemList = await getBK_MAIN_TODAY(thirdPartyAuthData, data);
        io.emit("receivedBKTodayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchBK_MAIN_TODAY(itemList)            
        }
        bkTodayInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_TODAY(thirdPartyAuthData, data);
            io.emit("receivedBKTodayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchBK_MAIN_TODAY(itemList)            
            }
        }, 300000);
    })

    socket.on("stopBKTodayMessage", () => {
        clearInterval(bkTodayInterval);
    })

    //=============== BK Early League Data ======================//

    socket.on("sendBKLeagueEarlyMessage", async () => {
        console.log("sendBKLeagueEarlyMessage");
        let result = await getBK_LEAGUE_EARLY(thirdPartyAuthData);
        io.emit("receivedBKLeagueEarlyMessage", result)
        leagueBKEarlyInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_EARLY(thirdPartyAuthData);
            io.emit("receivedBKLeagueEarlyMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueEarlyMessage", () => {
        clearInterval(leagueBKEarlyInterval);
    })



    //=============== BK Early Data ======================//

    socket.on("sendBKEarlyMessage", async (data) => {
        let itemList = await getBK_MAIN_EARLY(thirdPartyAuthData, data);
        io.emit("receivedBKEarlyMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchBK_MAIN_TODAY(itemList)            
        }
        bkEarlyInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_EARLY(thirdPartyAuthData, data);
            io.emit("receivedBKEarlyMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchBK_MAIN_TODAY(itemList)            
            }
        }, 300000);
    })

    socket.on("stopBKEarlyMessage", () => {
        clearInterval(bkEarlyInterval);
    })

    //=============== BK Champion League Data ======================//

    socket.on("sendBKLeagueChampionMessage", async () => {
        console.log("sendBKLeagueChampionMessage");
        let result = await getBK_LEAGUE_CHAMPION(thirdPartyAuthData);
        io.emit("receivedBKLeagueChampionMessage", result)
        leagueBKChampionInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_CHAMPION(thirdPartyAuthData);
            io.emit("receivedBKLeagueChampionMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueChampionMessage", () => {
        clearInterval(leagueBKChampionInterval);
    })

    //=============== BK Champion Data ======================//

    socket.on("sendBKChampionMainMessage", async (data) => {
        let itemList = await getBK_MAIN_CHAMPION(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedBKChampionMainMessage", itemList);
        if (Array.isArray(itemList) && itemList.length > 0) {
            dispatchBK_MAIN_CHAMPION(itemList) 
        } else if (itemList) {
            let tempItemList = [];
            tempItemList.push(itemList);
            dispatchBK_MAIN_CHAMPION(tempItemList)    
        }
        bkChampionInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_CHAMPION(thirdPartyAuthData, data);
            io.emit("receivedFTChampionMessage", itemList);
            if (Array.isArray(itemList) && itemList.length > 0) {
                dispatchBK_MAIN_CHAMPION(itemList) 
            } else if (itemList) {
                let tempItemList = [];
                tempItemList.push(itemList);
                dispatchBK_MAIN_CHAMPION(tempItemList)    
            }
        }, 300000);
    })

    socket.on("stopBKChampionMessage", () => {
        clearInterval(bkChampionInterval);
    })

    //=============== BK Parlay League Data ======================//

    socket.on("sendBKLeagueParlayMessage", async () => {
        console.log("sendBKLeagueParlayMessage");
        let result = await getBK_LEAGUE_PARLAY(thirdPartyAuthData);
        io.emit("receivedBKLeagueParlayMessage", result)
        leagueBKParlayInterval = setInterval( async () => {
            let result = await getBK_LEAGUE_PARLAY(thirdPartyAuthData);
            io.emit("receivedBKLeagueParlayMessage", result)
        }, 300000)
    })

    socket.on("stopBKLeagueParlayMessage", () => {
        clearInterval(leagueBKParlayInterval);
    })

    //=============== BK Parlay Data ======================//

    socket.on("sendBKParlayMessage", async (data) => {
        let itemList = await getBK_MAIN_PARLAY(thirdPartyAuthData, data);
        console.log(itemList)
        io.emit("receivedBKParlayMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchBK_MAIN_PARLAY(itemList)            
        }
        bkParlayInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_PARLAY(thirdPartyAuthData, data);
            io.emit("receivedBKParlayMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchBK_MAIN_PARLAY(itemList)            
            }
        }, 300000);
    })

    socket.on("stopBKParlayMessage", () => {
        clearInterval(bkParlayInterval);
    })

    //=============== BK Favorite Data ======================//

    socket.on("sendBKFavoriteMessage", async (data) => {
        clearInterval(bkFavoriteInterval);
        let itemList = await getBK_MAIN_FAVORITE(thirdPartyAuthData, data);
        io.emit("receivedBKFavoriteMessage", itemList);
        if (itemList && itemList.length > 0) {
            dispatchBK_MAIN_FAVORITE(itemList)            
        }
        bkFavoriteInterval = setInterval(async () => {
            let itemList = await getBK_MAIN_FAVORITE(thirdPartyAuthData, data);
            io.emit("receivedFTFavoriteMessage", itemList);
            if (itemList && itemList.length > 0) {
                dispatchBK_MAIN_FAVORITE(itemList)            
            }
        }, 300000);
    })

    socket.on("stopBKFavoriteMessage", () => {
        clearInterval(bkFavoriteInterval);
    })

});

app.get('/', (req, res) => {
    res.send("socket server");
});

server.listen(3000, () => {
    console.log('listening on *:3000');
});;