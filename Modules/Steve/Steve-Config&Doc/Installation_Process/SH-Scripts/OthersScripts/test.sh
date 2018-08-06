#!/bin/bash
# -*- ENCODING: UTF-8 -*-

  echo "Drupal 8 Install"
  echo " -- To use this script you need --"
  echo " - PHP > 5.6 "
  echo " - Composer"
  echo " - Drupar Console"
  echo " - Drush > 8.0.12"

 ## Variable

    #api
        _site=steveCMSnew
        _id_site=4

    #site
        _langcode=en
        _site_name=steveCMSclient
        _site_mail=contact@client1.dev
        _account_name=admin
        _account_mail=contact@client1.dev
        _account_pass=admin

    #Data Base
        _type=mysql
        _host=localhost
        _name=steve_cms
        _user=root
        _pass=root
        _port=3316

    #Drupal console
        _data=$(pwd)

        _url=$_data/$_site/console/
        echo $_url
        exit