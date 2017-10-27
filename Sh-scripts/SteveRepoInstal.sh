 #!/bin/bash
# -*- ENCODING: UTF-8 -*-

    echo "Drupal 8 Install"
    echo " -- To use this script you need --"
    echo " - PHP > 5.6 "
    echo " - Composer"
    echo " - Drupar Console"
    echo " - Drush 8"

 ## Variable

    #api
        _site=steveContentRepo

    #site
        _langcode=en
        _site_name=steve_ContentRepo
        _site_mail=contact@client1.dev
        _account_name=admin
        _account_mail=contact@client1.dev
        _account_pass=admin

    #Data Base
        _type=mysql
        _host=localhost
        _name=cms_repo
        _user=root
        _pass=root
        _port=3316

 ## Instalation

    composer create-project drupal/drupal $_site
    cd $_site
    composer require drupal/console:~1.0
    drupal init
    drupal site:install standard --langcode="$_langcode" --db-type="$_type" --db-host="$_host"  --db-name="$_name" --db-user="$_user" --db-pass="$_pass" --db-port="$_port" --site-name="$_site_name" --site-mail="$_site_mail" --account-name="$_account_name" --account-mail="$_account_mail" --account-pass="$_account_pass"
    composer global require drush/drush:8.*
    composer require guzzlehttp/guzzle-services
    echo "Cloning RP CMS Repo"
    git clone ssh://git@bitbucket.rebelpenguin.dk:7999/steve/steve-modules.git modules/custom
    mkdir modules/contrib
    drush en features, features_ui, devel, adminimal_theme, admin_toolbar, admin_toolbar_tools, prepropuplate --resolve-dependencies -y
    drush en rp_api, rp_base --resolve-dependencies -y
    drush en rp_channel, rp_competition, rp_game, rp_game_info, rp_language, rp_participant, rp_region, rp_repoapi, rp_site, rp_site_info, rp_sport, rp_sport_info, rp_stream_provider --resolve-dependencies -y
    drush rpapii languages
    drush rpapii channels
    drush rpapii regions
    drush rpapii sites
    drush rpapii sports
    drush rpapii stream_providers
    drush rpapii competitions
    drush rpapii participants --query="sport=4"
    drush rpapii participants --query="sport=1&competition=7"
    drush rpapii games --query="site=4&region=GB-ENG&lang=en_GB&start=2017-09-20"

    echo " Wi are done !!! Enjoy your new site. "
    exit