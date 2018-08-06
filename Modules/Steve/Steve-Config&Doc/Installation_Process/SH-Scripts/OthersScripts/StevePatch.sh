#!/bin/bash
# -*- ENCODING: UTF-8 -*-
   #1-
    cp modules/custom/RP_Configuration/Installation_Process/Patch/PathAuto/pathauto-drush-command-2717721-10.patch contrib/pathauto/
    cd  contrib/pathauto
    patch -p1 < pathauto-drush-command-2717721-10.patch
    cd ../../../
    drush cr all
    echo " Wi are done !!! Enjoy your new site. "
    exit