<?php

use Drupal\rp_site_api\Controller\site\siteAPIImporterController;
use Drupal\rp_site_api\Controller\site\siteAPIController;

/**
 * Implements hook_drush_command().
 */
function rp_site_api_drush_command() {

  $commands['rp-site-api-importAllSites'] = [
    'description' => 'Import All Site',
    'callback' => 'rp_site_api_drush_importAllSites',
    'aliases' => ['steve_importAllSites'],
    'examples' => [
      'drush steve_importAllSites' => 'Import All Site',

    ],
  ];


  $commands['rp-site-api-importSiteByID'] = [
    'description' => 'Import Site by Steve Site id',
    'callback' => 'rp_site_api_drush_importSiteByID',
    'aliases' => ['steve_importSiteByID'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush steve_importSiteByID 10' => 'Import Site by Steve Site id',
    ],
  ];

  return $commands;
}

function rp_site_api_drush_importAllSites(){
  $site = new siteAPIImporterController();
  return $site->importAllSites();
}
function rp_site_api_drush_importSiteByID($siteId){
  $site = new siteAPIImporterController();
  return $site->importSiteByID($siteId);
}
