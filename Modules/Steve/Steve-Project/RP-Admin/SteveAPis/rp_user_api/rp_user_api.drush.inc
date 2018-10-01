<?php

use Drupal\rp_user_api\Controller\user\userImportController;

/**
 * Implements hook_drush_command().
 */
function rp_user_api_drush_command() {

  $commands['rp-user-api-importAllUsers'] = [
    'description' => 'Get All Users',
    'callback' => 'rp_site_api_drush_import_users',
    'aliases' => ['steve_importUsers'],
    'examples' => [
      'drush steve_importUsers ' => 'Get All Users',
    ],
  ];

  $commands['rp-user-api-updateUserSiteDefault'] = [
    'description' => 'Get All User content by Steve SITE ID',
    'callback' => 'rp_site_api_drush_import_updateUserSiteDefault',
    'aliases' => ['steve_updateUsers'],
    'examples' => [
      'drush steve_updateUsers' => 'Get All User content by Steve SITE ID',
    ],
  ];

  $commands['rp-user-api-importUserBySiteID'] = [
    'description' => 'Get User by Steve SITE ID',
    'callback' => 'rp_site_api_drush_import_userBySiteID',
    'aliases' => ['steve_importUserBySiteID'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush steve_importUserBySiteID 10' => 'Get User by Steve SITE ID',
    ],
  ];

  $commands['rp-user-api-importUserContentBySiteID'] = [
    'description' => 'Get User by Steve SITE ID',
    'callback' => 'rp_site_api_drush_import_userContentBySiteID',
    'aliases' => ['steve_importUserContentBySiteID'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush steve_importUserContentBySiteID 10' => 'Get User by Steve SITE ID',
    ],
  ];

  $commands['rp-user-api-importUserSiteDefault'] = [
    'description' => 'Get User - where Steve SITE ID is taken from drupal configuration site id',
    'callback' => 'rp_site_api_drush_import_userSiteDefault',
    'aliases' => ['steve_importUserSiteDefault'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush steve_importUserSiteDefault' => 'Get User - where Steve SITE ID is taken from drupal configuration site id',
    ],
  ];

  return $commands;
}

function rp_site_api_drush_import_users(){
  $user = new userImportController();
  return $user->getAllUsers();
}
function rp_site_api_drush_import_userBySiteID($siteId){
  $user = new userImportController();
  return $user->getUserBySiteID($siteId);
}
function rp_site_api_drush_import_userContentBySiteID($siteId){
  $user = new userImportController();
  return $user->getUserContentBySiteID($siteId);
}
function rp_site_api_drush_import_userSiteDefault(){
  $user = new userImportController();
  return $user->getUserSiteDefault();
}
function rp_site_api_drush_import_updateUserSiteDefault(){
  $user = new userImportController();
  return $user->updateUserSiteDefault();
}
