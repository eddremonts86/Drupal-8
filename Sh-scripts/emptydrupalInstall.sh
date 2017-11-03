#!/bin/bash
# -*- ENCODING: UTF-8 -*-
  
  echo "Drupal 8 Install"
  echo " -- To use this script you need --"
  echo " - PHP > 5.6 "
  echo " - Composer"
  echo " - Drupar Console"
  echo " - Drush 8"
  
 ## Variable 
    _site=test
 ## Instalation

    composer create-project drupal/drupal $_site
    cd $_site
    composer require drupal/console:~1.0
    drupal init
    drupal site:install standard --langcode="en"  \
    --db-type="mysql" 
    --db-host="127.0.0.1"  
    --db-name="test2" 
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
  exit

