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
var { getSeverUrl } = require('./controllers/serverUrl.controller');
var { getFT_FU_R_TODAY } = require('./controllers/third_party_ft.controller');
var { getFT_FU_R_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_FU_R_EARLY } = require('./controllers/third_party_ft.controller');
var { getFT_PD } = require('./controllers/third_party_ft.controller');
var { getFT_FS } = require('./controllers/third_party_ft.controller');
var { getFT_HDP_OU_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORNER_INPLAY } = require('./controllers/third_party_ft.controller');
var { getFT_CORRECT_SCORE_INPLAY } = require('./controllers/third_party_ft.controller');
var { getLeagueCount } = require('./controllers/third_party_league.controller');

setInterval(() => {
    getUID_VER();
}, 1800000);

var obtIterval = 0;
var correctScoreInterval = 0;
var ftInPlayInterval = 0;
var receivedFTInPlayInterval = 0;
var receivedFTScoreInterval = 0;

io.on("connection", async function (socket) {

    // console.log("socket connected:" + socket.id);

    receivedFTInPlayInterval = setInterval(() => {
        axios.get(`${BACKEND_BASE_URL}${MATCH_SPORTS}${GET_IN_PLAY_DATA}`)
            .then(response => {
                if (response.status === 200) {
                    io.emit("receivedFTInPlayData", response.data.data);
                }
            })
    }, 4000);

    receivedFTScoreInterval = setInterval(() => {
        axios.get(`${BACKEND_BASE_URL}${MATCH_SPORTS}${GET_IN_PLAY_SCORE}`)
            .then(response => {
                if (response.status === 200) {
                    console.log(response.data);
                    io.emit("receivedFTInPlayScoreData", response.data.data);
                }
            })
    }, 4000);

    socket.on('userJoined', data => {
        console.log(data);
    });

    socket.on('sendHDP_OUData', data => {
        console.log(data);
        getFT_HDP_OU_INPLAY(data);
        clearInterval(obtIterval);
        obtIterval = setInterval(() => {
            getFT_HDP_OU_INPLAY(data);
        }, 8000)
    });

    socket.on('sendCornerData', data => {
        console.log(data);
        getFT_CORNER_INPLAY(data);
        clearInterval(obtIterval);
        obtIterval = setInterval(() => {
            getFT_CORNER_INPLAY(data);
        }, 8000)
    });

    socket.on('sendCorrectScoreMessage', () => {
        console.log("sendCorrectScoreMessage");
        clearInterval(receivedFTInPlayInterval);
        clearInterval(ftInPlayInterval);
        getFT_FU_R_INPLAY();
        getFT_CORRECT_SCORE_INPLAY();
        correctScoreInterval = setInterval(() => {
            getFT_CORRECT_SCORE_INPLAY();
        }, 60000)
    });

    socket.on('sendFTInPlayMessage', () => {
        console.log("sendFTInPlayMessage");
        clearInterval(receivedFTScoreInterval);
        clearInterval(receivedFTScoreInterval);
        getFT_FU_R_INPLAY();
        ftInPlayInterval = setInterval(() => {
            getFT_FU_R_INPLAY();
        }, 60000);
    });
});

server.listen(3000, () => {
    console.log('listening on *:3000');
});;