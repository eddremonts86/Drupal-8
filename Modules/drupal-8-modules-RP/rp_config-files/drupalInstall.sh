#!/bin/bash
# -*- ENCODING: UTF-8 -*-
  
  echo "Drupal 8 Install"
  echo " -- To use this script you need --"
  echo " - PHP > 5.6 "
  echo " - Composer"
  echo " - Drupar Console"
  echo " - Drush 8"
  
 ## Variable 

    _site=cmsfrontend.cu
    ##_id=1
    
 ## Install   
    composer create-project drupal/drupal $_site
    cd $_site
    composer require drupal/console:~1.0
    drupal init
    drupal site:install
    composer global require drush/drush:8.*

  echo "Cloning RP CMS Repo"
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-modules.git custom
    mkdir contrib

  echo "Instaling RP API Dependecies"
    cd custom
    composer install

  echo "Instaling modules Requires"
    drush en features, features_ui,devel, admin_toolbar, restui,shortcode,rp_shortcode,metatag,pathauto,rp_repo
    drush cache-clear drush
    
  echo "Instaling Themes and dependencies"  
    cd ../../themes
    mkdir contrib 
    drush en bootstrap
    
  echo "Cloning RP CMS Themes"
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-templates.git custom
    drush cache-clear drush

  echo "Import Data from API"
       drush rp_importfromapi

  echo "Changing permissions"
	cd ../
	sudo chmod 755 sites/default/settings.php       
	sudo chown -R edd:www-data *
	drush cache-clear drush
 	
  echo " Wi are done !!! Enjoy your new site. "

  exit

