#!/bin/bash
# -*- ENCODING: UTF-8 -*-
  
  echo "Drupal 8 Install"
  echo " -- To use this script you need --"
  echo " - PHP > 5.6 "
  echo " - Composer"
  echo " - Drupar Console"
  echo " - Drush 8"
  
 ## Variable 

    _site=changetoTaxon
    _id_site=4
    
 ## Instalation

    composer create-project drupal/drupal $_site
    cd $_site
    composer require drupal/console:~1.0
    drupal init
    ##drupal site:install
    drupal site:install standard --langcode="en"  \
    --db-type="mysql" 
    --db-host="127.0.0.1"  
    --db-name="test" 
    --db-user="root" 
    --db-pass="root" 
    --db-prefix="rp" 
    --db-port="3306" 
    --site-name="Client 1 Website" 
    --site-mail="contact@client1.dev" 
    --account-name="admin" 
    --account-mail="it@rebelpenguin.dk" 
    --account-pass="admin"

    composer global require drush/drush:8.*
    composer require guzzlehttp/guzzle-services
	
  echo "Cloning RP CMS Repo"
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-modules.git modules/custom
    mkdir modules/contrib

  echo "Instaling RP API Dependecies"
    cd modules/custom
    composer install

  echo "Instaling modules Requires"
    drush en features, features_ui,devel, admin_toolbar, restui,shortcode,rp_shortcode,metatag,pathauto
    drush cache-clear all


  echo "Instaling modules Requires"
    drush en rp_api, rp_base, rp_repoapi, rp_cms_site_info
    drush cache-clear all
    drush -y config-set rp_base.settings rp_base_site_api_id $_id_site
    drush rprepoapii site_info --query="filter[site][value]=$_id_site"  --update=1

  echo "Instaling Themes and dependencies"  
    cd ../../themes
    mkdir contrib 
    drush en bootstrap
    
  echo "Cloning RP CMS Themes"
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-templates.git custom
    drush cache-clear drush

  echo "Import Data from API"
    drush en rp_repo,sesport_blocks
    drush rp_importfromapi

  echo "Changing permissions"
	cd ../
	sudo chmod 755 sites/default/settings.php       
	sudo chown -R edd:www-data *
	drush cache-clear all
 	
  echo " Wi are done !!! Enjoy your new site. "

  exit

