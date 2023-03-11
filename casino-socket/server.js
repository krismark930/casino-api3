const app = require('express')();
const server = require('http').createServer(app);
const io = require('socket.io')(server);
const axios = require('axios');
var fs = require( 'fs' );
const { BACKEND_BASE_URL, MATCH_SPORTS, GET_FT_DATA, GET_IN_PLAY_DATA } = require('./api');
var { getSeverUrl } = require('./controllers/serverUrl.controller');
var { getFT_FU_R_TODAY, getFT_FU_R_INPLAY, getFT_FU_R_EARLY, getFT_PD, getFT_FS, getFT_HDP_OU_INPLAY } = require('./controllers/third_party_ft.controller');
var { getLeagueCount } = require('./controllers/third_party_league.controller');
var { getUID_VER } = require('./controllers/reload.controller');

const saveMsgUrl = "http://3.23.176.6/api/save-message";
const seenMsgUrl = "http://3.23.176.6/api/seen-message";
const disConnect = "http://3.23.176.6/api/disconnect-message";

setInterval(() => {
    // getFT_FU_R_TODAY();
    getFT_FU_R_INPLAY();
}, 60000);

// getLeagueCount();

// getFT_FU_R_TODAY();
getFT_FU_R_INPLAY();

setInterval(() => {
    getUID_VER();
}, 300000)

var HDP_OU_INTERVAL = 0;

io.on("connection", async function (socket) {

    // console.log("socket connected:" + socket.id);

    setInterval(() => {
        axios.get(`${BACKEND_BASE_URL}${MATCH_SPORTS}${GET_IN_PLAY_DATA}`)
            .then(response => {
                if (response.status === 200) {
                    io.emit("receivedFTInPlayData", response.data.data);
                }
            })
    }, 4000);

    socket.on('userJoined', data => {
        console.log(data);
    });

    socket.on('sendHDP_OUData', data => {
        console.log(data);
        getFT_HDP_OU_INPLAY(data);
        clearInterval(HDP_OU_INTERVAL);
        HDP_OU_INTERVAL = setInterval(() => {
            getFT_HDP_OU_INPLAY(data);
        }, 8000)
    });
});

server.listen(3000, () => {
    console.log('listening on *:3000');
});;