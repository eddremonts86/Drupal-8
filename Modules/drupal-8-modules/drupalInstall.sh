#!/bin/bash
# -*- ENCODING: UTF-8 -*-
echo "<° Script para instalar Drupal"

    _site=site_repo_
    _id=1
    composer create-project drupal/drupal $_site
    cd $_site
    composer require drupal/console:~1.0
    drupal init
    drupal site:install
    composer require guzzlehttp/guzzle-services
    touch rpconfig.php
    echo "<?php $site_id ="$_id"; ?>" > rpconfig.php
    cd modules
    git clone https://github.com/eddremonts86/drupal8-rp-modules.git 
    mkdir contrib
    drush en devel,admin_toolbar,rp_api,rp_region,rp_site,restui,rp_cms_stream_provider,rp_cms_game_pages,rp_cms_team_content,rp_cms_tournament_page
    #git init
    #git add -A
    #git commit -m "Initial Commit"

exit



