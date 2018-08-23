#!/usr/bin/env bash
# -*- ENCODING: UTF-8 -*-
echo "------------- All your Conf Vars --------------------
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
"11" -  ${11}  
"12" -  ${12}  
"13" -  ${13}  
"14" -  ${14}  
"15" -  ${15}  
"16" -  ${16}  
"17" -  ${17}  
"18" -  ${18}  
"19" -  ${19}  
"20" -  ${20}  
"21" -  ${21}  

"

echo " --------------File work -------------------"

cd sites
mkdir $1
echo " Crerating Folder ${1} "

echo " Crerating ${1} SETTINGS   "
cp default/default.settings.php $1/settings.php
#chmod -R 777 $1
#chown -R edd:www-data $1

echo "Writing in [core]/sites.php "
echo "\$sites['$1'] = '$1';" >> sites.php


echo " --------------Data base work -------------------
      Creating Database ${14}"

drush sql-create --db-su=${16} --db-su-pw=${17} --db-url="${13}://${16}:${17}@${14}/${15}" --yes
drush site-install standard --sites-subdir=$1 --db-url="${13}://${16}:${17}@${14}/${15}" --site-name=${8} --account-name=${10} --account-pass=${12} --site-mail=${11} --locale=${7} -y

echo " ---------------------------------"
echo "Installing drupal site  ${8} "
echo "---------------------------------"

cd $1
drush   --uri=$1 cr all

    echo " ---------------------------------"
    echo "Installing other core and contib modules "
    echo "---------------------------------"

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


    echo " ---------------------------------"
    echo "Instaling modules Requires "
    echo "---------------------------------"
        drush   --uri=$1  en  rp_repo --resolve-dependencies -y
        drush   --uri=$1  en  rp_ad_block --resolve-dependencies -y
        drush   --uri=$1  en  rp_cookie --resolve-dependencies -y
        drush   --uri=$1  en  rp_style --resolve-dependencies -y
        drush   --uri=$1  en  rp_layout --resolve-dependencies -y
        drush   --uri=$1  en  rp_client_base --resolve-dependencies -y
        drush   --uri=$1  en  rp_site_api --resolve-dependencies -y
        drush   --uri=$1  en  rp_user_api --resolve-dependencies -y
        drush   --uri=$1  en  drush_language --resolve-dependencies -y
    echo " --------------------------------- "
    echo "Instaling Themes and dependencies "
    echo "--------------------------------- "

        drush   --uri=$1  en bootstrap --resolve-dependencies -y
        drush   --uri=$1  en stevethemebase  --resolve-dependencies -y
        drush   --uri=$1  en rp_cms_steve_base_config  --resolve-dependencies -y

        # Exp config
        drush   --uri=$1  en $4  --resolve-dependencies -y
        drush   --uri=$1 config-set system.theme default $4 -y
        drush   --uri=$1  en $5  --resolve-dependencies -y

        #Enable default template
        drush   --uri=$1  en $6  --resolve-dependencies -y
        drush   --uri=$1  config-set system.theme admin $6 -y

        chmod -R 777 files
        drush --uri=$1 cr all

        drush --uri=$1 -y config-set rp_base.settings rp_base_site_api_id ${19}
        drush --uri=$1 -y config-set rp_base.settings rp_base_site_url ${20}
        drush --uri=$1 -y config-set rp_base.settings rp_base_site_url_api  ${21}
        drush --uri=$1 -y config-set rp_base.settings rp_base_def_channel  ${22}
        drush --uri=$1 rprepoapii site_info --query="filter[site][value]=${3}"  --update=1

        drush --uri=$1 rp_importSitesByID ${3}
        drush --uri=$1 steve_importSiteByID ${3}
        drush --uri=$1 steve_updateUsers
        drush --uri=$1 rp_importfromapi

        drush --uri=$1 langadd ${23}
        wget ${24}
        drush l--uri=$1 language-import ${23} ${25}

        drush --uri=$1 cr all

exit
