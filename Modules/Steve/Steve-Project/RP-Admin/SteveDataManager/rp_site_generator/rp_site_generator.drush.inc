<?php

use Drupal\rp_site_generator\Controller\siteGeneratorController;

/**
 * Implements hook_drush_command().
 */
function rp_site_generator_drush_command() {

  $commands['rp-site-generator-importAllSites'] = [
    'description' => 'RP API Import entities',
    'callback' => 'rp_site_generator_drush_generateSites',
    'aliases' => ['steve_generateSites'],
    'examples' => [
      'drush rpapii sites' => 'Import Sites entity from API STEVE',

    ],
  ];


  $commands['rp-site-generator-importSiteByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_site_generator_drush_generateSite',
    'aliases' => ['steve_generateSite'],
    'arguments' => [
      'siteId' => 'siteId'
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];

  return $commands;
}

function rp_site_generator_drush_generateSites(){
  $site = new siteGeneratorController();
  return $site->generateAllSites();
}
function rp_site_generator_drush_generateSite($siteId){
  $site = new siteGeneratorController();
  return $site->Generator($siteId);
}
