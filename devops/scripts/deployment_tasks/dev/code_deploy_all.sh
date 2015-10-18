#!/bin/bash

# this script is used for deploying code to all the services 

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

servers=$(< /home/$admin/devops/servers/$environment/allserviceservers.txt)
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

echo Starting the Code Deployment
date

##
echo deleting old backups
rm -rf /home/$admin/backups/*.tar.gz

##
echo backup old code
cd /home/$admin/ 

tar -zcvf backups/analytics.tar.gz code/analytics-service
tar -zcvf backups/artools.tar.gz code/artools-client
tar -zcvf backups/auth.tar.gz code/auth-service
tar -zcvf backups/bridge.tar.gz code/conductor-bridge
tar -zcvf backups/dataobjects.tar.gz code/conductor-dataobjects
tar -zcvf backups/gateway.tar.gz code/conductor-gateway
tar -zcvf backups/db-cron.tar.gz code/db-cron
tar -zcvf backups/email.tar.gz code/email-service
tar -zcvf backups/image.tar.gz code/image-processing-service
tar -zcvf backups/music.tar.gz code/music-service
tar -zcvf backups/profile.tar.gz code/profile-service
tar -zcvf backups/rec.tar.gz code/recommendation-service
tar -zcvf backups/song.tar.gz code/song-ingestion-service
tar -zcvf backups/user.tar.gz code/user-service
tar -zcvf backups/notation.tar.gz code/notation
tar -zcvf backups/clientapp.tar.gz code/$environment/clientapp
tar -zcvf backups/secret.tar.gz code/$environment/configs

##
echo copying backups to s3

cp /home/$admin/s3cfg_bkup /home/$admin/.s3cfg 

s3cmd put backups/analytics.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/artools.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/auth.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/bridge.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/dataobjects.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/gateway.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/db-cron.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/email.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/image.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/music.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/profile.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/rec.tar.gz s3://eardish.itops/$environment/code-bkups/
#s3cmd put backups/song.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/user.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/notation.tar.gz s3://eardish.itops/$environment/code-bkups/
#s3cmd put backups/clientapp.tar.gz s3://eardish.itops/$environment/code-bkups/
s3cmd put backups/secret.tar.gz s3://eardish.itops/$environment/code-bkups/

##
echo deleting old code

rm -rf code/analytics-service
rm -rf code/conductor-bridge
rm -rf code/db-cron
rm -rf code/music-service
rm -rf code/song-ingestion-service
rm -rf code/artools-client
rm -rf code/conductor-dataobjects
rm -rf code/email-service
rm -rf code/profile-service
rm -rf code/user-service
rm -rf code/auth-service
rm -rf code/conductor-gateway
rm -rf code/image-processing-service
rm -rf code/recommendation-service
rm -rf code/notation
rm -rf code/$environment/clientapp
rm -rf code/$environment/configs

##
echo cloning repos

cd /home/$admin/code

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
cd /home/$admin/code/$environment
#git clone git@github.com:eardish/clientapp.git
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
echo deploying code to servers

echo deploying gateway
for gateway_app in ${gateway[@]}
do
rsync -r -e ssh /home/$admin/code/conductor-gateway $admin@$gateway_app:$edservices
done

echo deploying bridge
for bridge_app in ${bridge[@]}
do
rsync -r -e ssh /home/$admin/code/conductor-bridge $admin@$bridge_app:$edservices
done

echo deploying email
for email_app in ${email[@]}
do
rsync -r -e ssh /home/$admin/code/email-service $admin@$email_app:$edservices
done

echo deploying auth
for auth_app in ${auth[@]}
do
rsync -r -e ssh /home/$admin/code/auth-service $admin@$auth_app:$edservices
done

echo deploying rec
for rec_app in ${rec[@]}
do
rsync -r -e ssh /home/$admin/code/recommendation-service $admin@$rec_app:$edservices
done

echo deploying song
for song_app in ${song[@]}
do
rsync -r -e ssh /home/$admin/code/song-ingestion-service $admin@$song_app:$edservices
done

echo deploying profile
for profile_app in ${profile[@]}
do
rsync -r -e ssh /home/$admin/code/profile-service $admin@$profile_app:$edservices
done

echo deploying music
for music_app in ${music[@]}
do
rsync -r -e ssh /home/$admin/code/music-service $admin@$music_app:$edservices
done

echo deploying image
for image_app in ${image[@]}
do
rsync -r -e ssh /home/$admin/code/image-processing-service $admin@$image_app:$edservices
done

echo deploying user
for user_app in ${user[@]}
do
rsync -r -e ssh /home/$admin/code/user-service $admin@$user_app:$edservices
done

echo deploying dbsvc
for dbsvc_app in ${dbsvc[@]}
do
rsync -r -e ssh /home/$admin/code/notation $admin@$dbsvc_app:$edservices
done

echo deploying analytics
for analytics_app in ${analytics[@]}
do
rsync -r -e ssh /home/$admin/code/analytics-service $admin@$analytics_app:$edservices
done

echo deploying artools
for artools_web in ${artools[@]}
do
rsync -r -e ssh /home/$admin/code/artools-client $admin@$artools_web:$edwebsites
done

#echo deploying client
#for client_web in ${client[@]}
#do
#rsync -r -e ssh /home/$admin/code/artools-client $admin@$client_web:$edwebsites
#done

##
echo composer update 

for gateway_app in ${gateway[@]}
do
ssh $admin@$gateway_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_gateway.sh
done

for bridge_app in ${bridge[@]}
do
ssh $admin@$bridge_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_bridge.sh
done

for email_app in ${email[@]}
do
ssh $admin@$email_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_email.sh
done

for auth_app in ${auth[@]}
do
ssh $admin@$auth_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_auth.sh
done

for rec_app in ${rec[@]}
do
ssh $admin@$rec_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_rec.sh
done

for song_app in ${song[@]}
do
ssh $admin@$song_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_song.sh
done

for profile_app in ${profile[@]}
do
ssh $admin@$profile_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_profile.sh
done

for music_app in ${music[@]}
do
ssh $admin@$music_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_music.sh
done

for image_app in ${image[@]}
do
ssh $admin@$image_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_image.sh
done

for user_app in ${user[@]}
do
ssh $admin@$user_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_user.sh
done

for dbsvc_app in ${dbsvc[@]}
do
ssh $admin@$dbsvc_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_dbsvc.sh
done

for analytics_app in ${analytics[@]}
do
ssh $admin@$analytics_app /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_analytics.sh
done

#for artools_web in ${artools[@]}
#do
#ssh $admin@$artools_web /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_artools.sh
#done

#for client_web in ${client[@]}
#do
#ssh $admin@$client_web /home/$admin/devops/scripts/deployment_tasks/task_composer/composer_client.sh
#done

##
echo starting cron

for server in ${servers[@]}
do
ssh $server "sudo service cron start"
done

##
echo reloading nginx

for server in ${artools[@]}
do
ssh $server "sudo service nginx reload"
done

for server in ${client[@]}
do
ssh $server "sudo service nginx reload"
done

echo jobs done
