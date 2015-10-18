#!/bin/bash

# This script is the template script

############################################################
## This section is for defining the environment variables ##

## Admin is the value used for the root user (ubuntu or edadmin)
 
#admin=edadmin
admin=ubuntu

## Environment is the value used for the environment (local, dev, qa, stg, or prod)

#environment=local
#environment=dev
#environment=qa
#environment=stg
environment=prod

## The below listing is for text files that contain a list of all the servers participating in a service or site or all for the environment

servers=$(< /home/$admin/devops/servers/$environment/allservers.txt)
analytics=$(< /home/$admin/devops/servers/$environment/service_all.txt)
auth=$(< /home/$admin/devops/servers/$environment/service_all.txt)
artools=$(< /home/$admin/devops/servers/$environment/artools.txt)
bridge=$(< /home/$admin/devops/servers/$environment/bridge_all.txt)
dbsvc=$(< /home/$admin/devops/servers/$environment/dbsvc_all.txt)
email=$(< /home/$admin/devops/servers/$environment/service_all.txt)
gateway=$(< /home/$admin/devops/servers/$environment/gateway_all.txt)
image=$(< /home/$admin/devops/servers/$environment/service_all.txt)
music=$(< /home/$admin/devops/servers/$environment/service_all.txt)
profile=$(< /home/$admin/devops/servers/$environment/service_all.txt)
rec=$(< /home/$admin/devops/servers/$environment/service_all.txt)
song=$(< /home/$admin/devops/servers/$environment/service_all.txt)
user=$(< /home/$admin/devops/servers/$environment/service_all.txt)

## The below values define paths for this environment

edwebsites="/eda/web"
edservices="/eda/app"
edlogs="/eda/logs"
configs="/eda/secret"

## This is the end of the environment variable section  ##
##########################################################

######################################
## Start the script functions below ##

echo creating base folders

sudo mkdir /eda
sudo chmod 777 /eda
sudo chown $admin /eda
sudo chgrp $admin /eda

mkdir /eda/secret
mkdir /eda/app
mkdir /eda/logs
mkdir /eda/web

echo creating /secret temp symlink

sudo ln -s /eda/secret /secret
#mkdir /secret

echo Jobs done!
