#!/bin/bash

ls 
pwd
# npm install 

if [ "${EVN}" = "pre" ];then
    echo "pre"
    cp -f ./.env_pre ./.env
else
    echo "pro"
    cp -f ./.env_pro ./.env

    if [ "${TARGET}" = "jxc" ];then
        cp -f ./.env_pro_jxc ./.env
    fi

    if [ "${TARGET}" = "xyh" ];then
        cp -f ./.env_pro ./.env
    fi
    
fi