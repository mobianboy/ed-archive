#!/bin/bash

# this script is used for deploying configs to all the services 

############################################################
## This section is for defining the environment variables ##

## Admin is the value used for the root user (ubuntu or edadmin)

#admin=$USER
#admin=edadmin
admin=ubuntu

## Environment is the value used for the environment (local, dev, qa, stg, or prod)

#environment=local
#environment=dev
environment=qa
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

echo Starting the Config Deployment
date

##
echo deleting old backups
rm -rf /home/$admin/backups/secret.tar.gz

##
echo backup old configs
cd /home/$admin/ 
tar -zcvf backups/secret.tar.gz /home/$admin/code/$environment/configs/secret

##
echo copying backups to s3

cp /home/$admin/s3cfg_bkup /home/$admin/.s3cfg 

s3cmd put backups/secret.tar.gz s3://eardish.itops/$environment/code-bkups/

##
echo deleting old config

rm -rf /home/$admin/code/$environment/configs

echo deleting old config completed

##
echo cloning repos

cd /home/$admin/code/$environment
git clone git@github.com:eardish/configs.git
cd /home/$admin

##
echo stopping cron

for server in ${servers[@]}
do
ssh $server "sudo service cron stop"
done

##
echo stopping php services

for server in ${servers[@]}
do
ssh "$server" 'bash -s' < /home/$admin/devops/scripts/services/$environment/stop_PHP_services.sh
done

##
echo deploying config to servers
for server in ${servers[@]}
do
rsync -r -e ssh /home/$admin/code/$environment/configs/secret/ $admin@$server:$configs
done

##
echo starting cron

for server in ${servers[@]}
do
ssh $server "sudo service cron start"
done

echo jobs done
