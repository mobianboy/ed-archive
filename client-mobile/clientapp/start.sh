#!/bin/bash

# Souce NVM and run `npm start`
# source nvm because .bashrc does nothing when loaded non-interactively
#source /home/vagrant/.nvm/nvm.sh
#cd /home/vagrant/clientapp/
#
## IF Logging is set on, don't run in background
#if [[ $(cat .env | grep -c GULP_LOG_TO_CONSOLE=TRUE) > 0 ]]; then
#  echo "would start npm"
##  npm start
#else
#  echo "would start npm"
##  nohup npm start &
#fi

# Start up Xvfb so chrome runs
Xvfb :1 +extension RANDR -screen 5 1024x768x8 >~/clientapp/logs/xvfb/$(date +%F).log 2>&1 &
export DISPLAY=:1.5

if [[ $(cat ~/clientapp/.env | grep -c VAGRANT_RUN_GULP_ON_START=TRUE) > 0 ]]; then
  source ~/clientapp/helpers.sh
  start-gulp
fi
