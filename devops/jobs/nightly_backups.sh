#!/bin/bash

# this script is used for nightly code backups of all the repos

############################################################
## This section is for defining the environment variables ##

## Admin is the value used for the root user (ubuntu or edadmin)

#admin=$USER
#admin=edadmin
admin=ubuntu

## Environment is the value used for the environment (local, dev, qa, stg, or prod)

#environment=local
#environment=dev
#environment=qa
#environment=stg
#environment=prod

## The below listing is for text files that contain a list of all the servers participating in a service or site or all for the environment

#servers=$(< /home/$admin/devops/servers/$environment/allserviceservers.txt)
#analytics=$(< /home/$admin/devops/servers/$environment/analytics.txt)
#auth=$(< /home/$admin/devops/servers/$environment/auth.txt)
#artools=$(< /home/$admin/devops/servers/$environment/artools.txt)
#bridge=$(< /home/$admin/devops/servers/$environment/bridge.txt)
#dbsvc=$(< /home/$admin/devops/servers/$environment/dbsvc.txt)
#email=$(< /home/$admin/devops/servers/$environment/email.txt)
#gateway=$(< /home/$admin/devops/servers/$environment/gateway.txt)
#image=$(< /home/$admin/devops/servers/$environment/image.txt)
#music=$(< /home/$admin/devops/servers/$environment/music.txt)
#profile=$(< /home/$admin/devops/servers/$environment/profile.txt)
#rec=$(< /home/$admin/devops/servers/$environment/rec.txt)
#song=$(< /home/$admin/devops/servers/$environment/song.txt)
#user=$(< /home/$admin/devops/servers/$environment/user.txt)
#client=$(< /home/$admin/devops/servers/$environment/client.txt)

## The below values define paths for this environment

edwebsites="/eda/web"
edservices="/eda/app"
edlogs="/eda/logs"
configs="/eda/secret"

## This is the end of the environment variable section  ##
##########################################################

######################################
## Start the script functions below ##

echo Starting the Code Backup
date

##
echo deleting old backups
rm -rf /home/$admin/nightly/nightly_master.tar.gz
rm -rf /home/$admin/nightly/nightly_default.tar.gz

##
echo deleting old code

cd /home/$admin/nightly
sudo rm -rf master
sudo rm -rf default

##
echo creating folders

cd /home/$admin/nightly
mkdir master
mkdir default

##
echo cloning master repos

cd /home/$admin/nightly/master
echo cd nightly/master

git clone git@github.com:eardish/artools-client.git -b master
git clone git@github.com:eardish/conductor-gateway.git -b master
git clone git@github.com:eardish/email-service.git -b master
git clone git@github.com:eardish/auth-service.git -b master
git clone git@github.com:eardish/conductor-bridge.git -b master
git clone git@github.com:eardish/user-service.git -b master
git clone git@github.com:eardish/profile-service.git -b master
git clone git@github.com:eardish/recommendation-service.git -b master
git clone git@github.com:eardish/analytics-service.git -b master
git clone git@github.com:eardish/music-service.git -b master
git clone git@github.com:eardish/image-processing-service.git -b master
git clone git@github.com:eardish/song-ingestion-service.git -b master
git clone git@github.com:eardish/conductor-dataobjects.git -b master
git clone git@github.com:eardish/db-cron.git -b master
git clone git@github.com:eardish/notation.git -b master
git clone git@github.com:eardish/clientapp.git -b master
git clone git@github.com:eardish/configs.git -b master
git clone git@github.com:eardish/devops.git -b master
git clone git@github.com:eardish/client-mobile.git -b master
git clone git@github.com:eardish/conductor-dataobjects.git -b master
git clone git@github.com:eardish/eardish.git -b master
git clone git@github.com:eardish/jobs.git -b master
git clone git@github.com:eardish/scripts.git -b master
git clone git@github.com:eardish/gamification-service.git -b master
git clone git@github.com:eardish/website.git -b master
git clone git@github.com:eardish/client-cordova.git -b master
git clone git@github.com:eardish/deployment.git -b master
git clone git@github.com:eardish/conductor-sandbox.git -b master
git clone git@github.com:eardish/conductor-service-base.git -b master
git clone git@github.com:eardish/demos.git -b master

echo master clone done

##
echo cloning default repos

cd /home/$admin/nightly/default
echo dc nightly/default

git clone git@github.com:eardish/artools-client.git
git clone git@github.com:eardish/conductor-gateway.git
git clone git@github.com:eardish/email-service.git
git clone git@github.com:eardish/auth-service.git
git clone git@github.com:eardish/conductor-bridge.git
git clone git@github.com:eardish/user-service.git
git clone git@github.com:eardish/profile-service.git
git clone git@github.com:eardish/recommendation-service.git
git clone git@github.com:eardish/analytics-service.git
git clone git@github.com:eardish/music-service.git
git clone git@github.com:eardish/image-processing-service.git
git clone git@github.com:eardish/song-ingestion-service.git
git clone git@github.com:eardish/conductor-dataobjects.git
git clone git@github.com:eardish/db-cron.git
git clone git@github.com:eardish/notation.git
git clone git@github.com:eardish/clientapp.git
git clone git@github.com:eardish/configs.git
git clone git@github.com:eardish/devops.git
git clone git@github.com:eardish/client-mobile.git
git clone git@github.com:eardish/conductor-dataobjects.git
git clone git@github.com:eardish/eardish.git
git clone git@github.com:eardish/jobs.git
git clone git@github.com:eardish/scripts.git
git clone git@github.com:eardish/gamification-service.git
git clone git@github.com:eardish/website.git
git clone git@github.com:eardish/client-cordova.git
git clone git@github.com:eardish/deployment.git
git clone git@github.com:eardish/conductor-sandbox.git
git clone git@github.com:eardish/conductor-service-base.git
git clone git@github.com:eardish/demos.git

echo default clone done

##
echo create tarballs of the code
cd /home/$admin/nightly 

tar -zcvf nightly_master.tar.gz master
tar -zcvf nightly_default.tar.gz default

##
echo copying backups to s3

cp /home/$admin/s3cfg_bkup /home/$admin/.s3cfg 
cd /home/$admin/nightly

s3cmd put nightly_master.tar.gz s3://eardish.itops/nightly/code-bkups/
s3cmd put nightly_default.tar.gz s3://eardish.itops/nightly/code-bkups/


echo jobs done
