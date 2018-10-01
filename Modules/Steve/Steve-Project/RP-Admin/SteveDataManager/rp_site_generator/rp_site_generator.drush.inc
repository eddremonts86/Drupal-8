<?php

use Drupal\rp_site_generator\Controller\siteGeneratorController;

/**
 * Implements hook_drush_command().
 */
function rp_site_generator_drush_command() {

  $commands['rp-site-generator-importAllSites'] = [
    'description' => 'Generate All sites with a config file',
    'callback' => 'rp_site_generator_drush_generateSites',
    'aliases' => ['steve_generateSites'],
    'examples' => [
      'drush steve_generateSites' => 'Generate All sites with a config file',

    ],
  ];

  $commands['rp-site-generator-importSiteByID'] = [
    'description' => 'Generate sites by Steve Site ID with a config file',
    'callback' => 'rp_site_generator_drush_generateSite',
    'aliases' => ['steve_generateSite'],
    'arguments' => [
      'siteId' => 'siteId'
    ],
    'examples' => [
      'drush steve_generateSite 10' => 'Generate sites by Steve Site ID with a config file',
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
