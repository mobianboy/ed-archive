#!/bin/bash

#This script will check to see if Song Ingestion php process is running and if not then it will start the service

############################################################
## This section is for defining the environment variables ##

## Admin is the value used for the root user (ubuntu or edadmin)

#admin=$USER
admin=edadmin
#admin=ubuntu

## Environment is the value used for the environment (local, dev, qa, stg, or prod)

#environment=local
environment=dev
#environment=qa
#environment=stg
#environment=prod

## The below listing is for text files that contain a list of all the servers participating in a service or site or all for the environment

servers=$(< /home/$admin/devops/servers/$environment/allservers.txt)
analytics=$(< /home/$admin/devops/servers/$environment/analytics.txt)
auth=$(< /home/$admin/devops/servers/$environment/auth.txt)
artools=$(< /home/$admin/devops/servers/$environment/artools.txt)
bridge=$(< /home/$admin/devops/servers/$environment/bridge.txt)
dbsvc=$(< /home/$admin/devops/servers/$environment/dbsvc.txt)
email=$(< /home/$admin/devops/servers/$environment/email.txt)
gateway=$(< /home/$admin/devops/servers/$environment/gateway.txt)
image=$(< /home/$admin/devops/servers/$environment/image.txt)
music=$(< /home/$admin/devops/servers/$environment/music.txt)
profile=$(< /home/$admin/devops/servers/$environment/profile.txt)
rec=$(< /home/$admin/devops/servers/$environment/rec.txt)
song=$(< /home/$admin/devops/servers/$environment/song.txt)
user=$(< /home/$admin/devops/servers/$environment/user.txt)
client=$(< /home/$admin/devops/servers/$environment/client.txt)

## The below values define paths for this environment

edwebsites="/eda/web"
edservices="/eda/app"
edlogs="/eda/logs"
configs="/eda/secret"

## This is the end of the environment variable section  ##
##########################################################

######################################
## Start the script functions below ##

#if pgrep --exact php &>/dev/null; then
if "ps -eo pid,command | awk '!/awk/ && /song/ {print $1}'" &>/dev/null; then
    printf "PHP is running\n"
else
   echo starting php service
   bash  /home/$admin/devops/scripts/services/$environment/start_song.sh
fi
