<?php

use Drupal\rp_push_api\Controller\push\pushImport;

/**
 * Implements hook_drush_command().
 */
function rp_push_api_drush_command() {

    $commands['rp-push-api-importSiteByID'] = [
    'description' => 'Import Site by Steve Site id',
    'callback' => 'rp_push_api_drush_steve_importClientToken',
    'aliases' => ['steve_importClientToken'],
    'arguments' => [
      'ctoken' => 'ctoken'
    ],
    'examples' => [
      'drush steve_importSiteByID 10' => 'Import Site by Steve Site id',
    ],
  ];

  return $commands;
}

function rp_push_api_drush_steve_importClientToken($ctoken){
  $site = new pushImport();
  return $site->importSiteConfig($ctoken);
}
