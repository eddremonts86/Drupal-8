#!/usr/bin/env bash
# -*- ENCODING: UTF-8 -*-

echo "------------- All your Conf Vars --------------------<br>
echo
"1" -  ${1}  
"2" -  ${2} 
"3" -  ${3} 
"4" -  ${4} 
"5" -  ${5} 
"6" -  ${6} 
"7" -  ${7} 
"8" -  ${8} 
"9" -  ${9}
"10" -  ${10}
"

echo "<br> --------------File work -------------------<br>"
cd sites
cp -R  $9 $1
rm $1/settings.php

mkdir $1/config-export
echo " Crerating Folder ${1}"

echo " Crerating ${1} SETTINGS  "
cp default/default.settings.php $1/settings.php
#chmod -R 777 $1
#chown -R edd:www-data $1

echo "Writing in [core]/sites.php"
echo "\$sites['$1'] = '$1';" >> sites.php


echo "<br> --------------Data base work -------------------<br>
      Creating Database ${5}<br>"

drush sql-create --db-su=${6} --db-su-pw=${7} --db-url="${3}://${6}:${7}@${4}/${5}" --yes
drush site-install minimal --sites-subdir=$1 --db-url="${3}://${6}:${7}@${4}/${5}"   --site-name=temp --account-name=temp --account-pass=temp --site-mail=temp@temp.com --locale=en -y



_url=/home/edd/All-Projects/user.controller.cu/sites/$1/config-export/

drush --uri=$9 sql-dump >  $1/config-export/$9.sql --yes

drush --uri=$1 sql-drop --yes
drush --uri=$1 sql-cli < $1/config-export/$9.sql --yes

drush --uri=${10} config-export --destination=$_url --yes
drush --uri=$1    cr all
drush --uri=$1    config-import --source=$_url -y

drush --uri=$1 -y config-set rp_base.settings rp_base_site_url ${1}
drush --uri=$1    cr all

exit
