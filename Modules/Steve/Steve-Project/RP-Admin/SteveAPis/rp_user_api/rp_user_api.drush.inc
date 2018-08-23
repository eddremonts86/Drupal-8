<?php

use Drupal\rp_user_api\Controller\user\userImportController;

/**
 * Implements hook_drush_command().
 */
function rp_user_api_drush_command() {

  $commands['rp-user-api-importAllUsers'] = [
    'description' => 'RP API Import entities',
    'callback' => 'rp_site_api_drush_import_users',
    'aliases' => ['steve_importUsers'],
    'examples' => [
      'drush rpapii sites' => 'Import Sites entity from API STEVE',

    ],
  ];
  $commands['rp-user-api-updateUserSiteDefault'] = [
    'description' => 'RP API Import entities',
    'callback' => 'rp_site_api_drush_import_updateUserSiteDefault',
    'aliases' => ['steve_updateUsers'],
    'examples' => [
      'drush rpapii sites' => 'Import Sites entity from API STEVE',

    ],
  ];

  $commands['rp-user-api-importUserBySiteID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_site_api_drush_import_userBySiteID',
    'aliases' => ['steve_importUserBySiteID'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];

  $commands['rp-user-api-importUserContentBySiteID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_site_api_drush_import_userContentBySiteID',
    'aliases' => ['steve_importUserContentBySiteID'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];

  $commands['rp-user-api-importUserSiteDefault'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_site_api_drush_import_userSiteDefault',
    'aliases' => ['steve_importUserSiteDefault'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
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
