#!/bin/bash

docker login  -u casino -p casinoEHAcf6pTU https://docker.flct.io/

if [ "${EVN}" = "pre" ];then
    docker tag pre-casino-api:${VERSION}-${BUILD_ID} docker.flct.io/casino/pre-casino-api:${VERSION}-${BUILD_ID}
    docker push docker.flct.io/casino/casino-api:${VERSION}-${BUILD_ID}
else
    docker tag casino-api:${VERSION}-${BUILD_ID} docker.flct.io/casino/casino-api:${VERSION}-${BUILD_ID}
    docker push docker.flct.io/casino/casino-api:${VERSION}-${BUILD_ID}
fi