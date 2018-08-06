#!/bin/bash
# -*- ENCODING: UTF-8 -*-

echo "------------- All your Conf Vars --------------------<br>
echo
"1" -  ${1}   <br>
"2" -  ${2}  <br>
"3" -  ${3}  <br>
"4" -  ${4}  <br>
"5" -  ${5}  <br>
"6" -  ${6}  <br>
"7" -  ${7}  <br>
"8" -  ${8}  <br>
"9" -  ${9}  <br>
"10" -  ${10}  <br>
"11" -  ${11}  <br>
"12" -  ${12}  <br>
"13" -  ${13}  <br>
"14" -  ${14}  <br>
"15" -  ${15}  <br>
"16" -  ${16}  <br>
"17" -  ${17}  <br>
"18" -  ${18}  <br>
"

echo "<br> --------------File work -------------------<br>"

cd sites
mkdir $1
echo " Crerating Folder ${1} <br>"

echo " Crerating ${1} SETTINGS   <br>"
cp default/default.settings.php $1/settings.php
#chmod -R 777 $1
#chown -R edd:www-data $1

echo "Writing in [core]/sites.php <br>"
echo "\$sites['$1'] = '$1';" >> sites.php


echo "<br> --------------Data base work -------------------<br>
      Creating Database ${14}<br>"

#Create postgreSql db - pending
mysql -u ${15} -p${16} <<MY_QUERY
CREATE DATABASE IF NOT EXISTS ${14}
MY_QUERY


echo "<br> ---------------------------------<br>"
echo "Installing drupal site  ${7} <br>"
echo "---------------------------------<br>"

echo "<br><br>
    --uri=$1  site:install standard <br>
    --langcode="$6" <br>
    --db-type="${12}" <br>
    --db-host="${13}" <br>
    --db-name="${14}" <br>
    --db-user="${15}" <br>
    --db-pass="${16}" <br>
    --db-port="${17}" <br>
    --db-prefix="stevecms_" <br>
    --site-name="$7" <br>
    --site-mail="$8" <br>
    --account-name="${9}" <br>
    --account-mail="${10}" <br>
    --account-pass="${11}" <br><br><br>
    "
pwd
cd $1

drush site-install standard --sites-subdir=$1 --db-url='mysql://'${15}':'${16}'@'${13}'/'${14}'' --site-name=${7} --account-name=${9} --account-pass=${11} --site-mail=${10} --locale=${6} -y
drush   --uri=$1 cr all

#drupal --uri=$1  site:install standard --langcode="$6" --db-type="${12}" --db-host="${13}" --db-name="${14}" --db-user="${15}" --db-pass="${16}" --db-port="${17}" --db-prefix="stevecms_" --site-name="$7" --site-mail="$8" --account-name="${9}" --account-mail="${10}" --account-pass="${11}" --force
#drupal --uri=$1  cr all


#chmod 755 $1/settings.php
    echo "<br> ---------------------------------<br>"
    echo "Installing other core and contib modules <br>"
    echo "---------------------------------<br>"

        drush   --uri=$1  en  media  --resolve-dependencies -y
        drush   --uri=$1  en  admin_toolbar_tools  --resolve-dependencies -y
        drush   --uri=$1  en  admin_toolbar_links_access_filter  --resolve-dependencies -y
        drush   --uri=$1  en  ctools_views  --resolve-dependencies -y
        drush   --uri=$1  en  field_layout  --resolve-dependencies -y
        drush   --uri=$1  en  layout_builder  --resolve-dependencies -y
        drush   --uri=$1  en  devel  --resolve-dependencies -y
        drush   --uri=$1  en  devel_generate  --resolve-dependencies -y
        drush   --uri=$1  en  features  --resolve-dependencies -y
        drush   --uri=$1  en  field_group  --resolve-dependencies -y
        drush   --uri=$1  en  shortcode  --resolve-dependencies -y
        drush   --uri=$1  en  panelizer  --resolve-dependencies -y
        drush   --uri=$1  en  panelizer_quickedit  --resolve-dependencies -y
        drush   --uri=$1  en  pathauto  --resolve-dependencies -y
        drush   --uri=$1  en  twig_blocks  --resolve-dependencies -y
        drush   --uri=$1  en  schema_metatag --resolve-dependencies -y


    echo "<br> ---------------------------------<br>"
    echo "Instaling modules Requires <br>"
    echo "---------------------------------<br>"
        drush   --uri=$1  en  rp_repo --resolve-dependencies -y
        drush   --uri=$1  en  rp_ad_block --resolve-dependencies -y
        drush   --uri=$1  en  rp_cookie --resolve-dependencies -y
        drush   --uri=$1  en  rp_style --resolve-dependencies -y
        drush   --uri=$1  en  rp_layout --resolve-dependencies -y
        drush   --uri=$1  en  rp_client_base --resolve-dependencies -y
        drush   --uri=$1  en  rebel_endpoints --resolve-dependencies -y

    echo "<br> --------------------------------- <br>"
    echo "Instaling Themes and dependencies <br>"
    echo "--------------------------------- <br>"

        # Exp config
        drush   --uri=$1  en $3  --resolve-dependencies -y
        drush   --uri=$1 config-set system.theme default $3 -y
        drush   --uri=$1  en $4  --resolve-dependencies -y

        #Enable default template
        drush   --uri=$1  en $5  --resolve-dependencies -y
        drush   --uri=$1  config-set system.theme admin $5 -y


        drush --uri=$1 -y config-set rp_base.settings rp_base_site_api_id ${18}
        drush --uri=$1 rprepoapii site_info --query="filter[site][value]=${18}"  --update=1

        drush --uri=$1 rp_importSitesByID ${18}
        drush --uri=$1 rp_importfromapi

        chmod -R 777 site/$1/files
        drush --uri=$1 cr all



exit
