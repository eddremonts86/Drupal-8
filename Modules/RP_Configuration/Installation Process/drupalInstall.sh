#!/bin/bash
# -*- ENCODING: UTF-8 -*-
  
  echo "This script assumes your environment has at least the following requirements:"
  echo " - PHP > 5.6 "
  echo " - Composer"
  echo " - Drupal Console + drupal init "
  echo " - Drush 8"
  
# TODO: DrupalConsole requires that the init process has been performed before starting using it.
# The process is interactive, and cannot be automated, so in order to avoid the script needing manual
# intervention, we will need to copy the files on "drupalconsole" folder to /home/vagrant/.console/
# or /etc/console
 
 ##Input vars 
_site=destination_folder
_id_site=4 # WatchFooty.co.uk

## SETUP PROCESS
# TODO: We might need to lock dependencies with specific versions to avoid this crashing because of untested updates
# e.g.        composer require drupal/console:~1.0
# instead of  composer require drupal/console:@stable

# Create the drupal project (like a git checkout) and place it on the destination folder
composer create-project drupal/drupal $_site
cd $_site
# Add DrupalConsole
composer require drupal/console:~1.0
# Perform Drupal setup
# Database credentials/host need to be replaced
# The script expects that a database is already set up in a PostgreSQL server somewhere, with its own user, password.
# e.g. a script like this run with the postgres user:
#
# CREATE ROLE username WITH LOGIN PASSWORD 'password';
# CREATE DATABASE client1;
# GRANT ALL PRIVILEGES ON DATABASE client1 TO username;
# ALTER DATABASE "client1" SET bytea_output = 'escape';
#
# last step is important, Drupal will not install without the proper database encoding. 
#
# user needs to be created with psql because of the password prompt. database can be created with this command as postgres user though:
# createdb --encoding=UTF8 --owner=username client1
#
# result should be something like this:
# postgres=# \l
#                                  List of databases
#   Name    |  Owner   | Encoding |   Collate   |    Ctype    |   Access privileges
# -----------+----------+----------+-------------+-------------+-----------------------
#  client1   | username | UTF8     | en_US.utf8  | en_US.utf8  |

drupal site:install standard --langcode="en"  \
    --db-type="pgsql" 
    --db-host="127.0.0.1"  
    --db-name="client1" 
    --db-user="username" 
    --db-pass="password" 
    --db-prefix="rp" 
    --db-port="5432" 
    --site-name="Client 1 Website" 
    --site-mail="contact@client1.dev" 
    --account-name="admin" 
    --account-mail="it@rebelpenguin.dk" 
    --account-pass="password"

# Add drush and guzzle
composer global require drush/drush:8.*
composer require guzzlehttp/guzzle-services

# Time to get our modules and place them into the website 
# A SSH key will be needed to clone the repo (we probably don't want to mount the modules/custom folder from outside)
git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-modules.git modules/custom

# Install API dependencies
cd modules/custom
composer install

# Install required modules
mkdir modules/contrib
mkdir themes/contrib
drush en features, features_ui, devel, adminimal_theme, admin_toolbar, restui, shortcode, rp_shortcode, metatag, pathauto, rp_api, rp_base, rp_repoapi, rp_cms_site_info, bootstrap, rp_repo --resolve-dependencies -y
drush cache-rebuild all
drush -y config-set rp_base.settings rp_base_site_api_id $_id_site
drush rprepoapii site_info --query="filter[site][value]=$_id_site" --update=1
 
# Install relevant theme 
# TODO: figure out what is the theme that needs to be used
git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-templates.git custom
drush en sesport_blocks
drush cache-rebuild all

# This step is meant to be executed only on site creation, never on updates
# For updates, this needs to be a cron job, the script needs to place the command in crontab after the setup is finished

# Import Data from API
drush rp_importfromapi

