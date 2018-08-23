<?php

use Drupal\rp_site_api\Controller\site\siteAPIImporterController;
use Drupal\rp_site_api\Controller\site\siteAPIController;

/**
 * Implements hook_drush_command().
 */
function rp_site_api_drush_command() {

  $commands['rp-site-api-importAllSites'] = [
    'description' => 'RP API Import entities',
    'callback' => 'rp_site_api_drush_importAllSites',
    'aliases' => ['steve_importAllSites'],
    'examples' => [
      'drush rpapii sites' => 'Import Sites entity from API STEVE',

    ],
  ];


  $commands['rp-site-api-importSiteByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_site_api_drush_importSiteByID',
    'aliases' => ['steve_importSiteByID'],
    'arguments' => [
     'siteId' => 'siteId'
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
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
