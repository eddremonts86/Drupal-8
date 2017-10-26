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
    drupal init22
    drupal site:install
    composer global require drush/drush:8.*
    composer require guzzlehttp/guzzle-services

  echo "Cloning RP CMS Repo"
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-modules.git modules/custom
    mkdir modules/contrib
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-templates.git themes/custom
    mkdir themes/contrib

  echo "Instaling Themes and dependencies"
    drush en bootstrap
    drush en stevethemebase


  echo "Instaling modules Requires"
    drush en rp_cms_steve_integration
    drush en rp_repo
    drush -y config-set rp_base.settings rp_base_site_api_id $_id_site
    drush rprepoapii site_info --query="filter[site][value]=$_id_site"  --update=1

    drush cache-rebuild all
	drush rp_importfromapi

  echo "Changing permissions"
	cd ../
	sudo chmod 755 sites/default/settings.php
	sudo chown -R edd:www-data *
	drush cache-rebuild all

  echo " Wi are done !!! Enjoy your new site. "

  exit

