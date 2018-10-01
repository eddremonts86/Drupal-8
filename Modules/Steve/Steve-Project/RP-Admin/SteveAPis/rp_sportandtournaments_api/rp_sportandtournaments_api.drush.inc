<?php

use Drupal\rp_sportandtournaments_api\Controller\generalController;
/**
 * Implements hook_drush_command().
 */

function rp_sportandtournaments_api_drush_command() {
  $commands['rp-sportandtournaments-api-importAllSports'] = [
    'description' => 'Import Sports and tournaments',
    'callback' => 'rp_sportandtournaments_api_drush_importAllSports',
    'aliases' => ['steve_importAllSports'],
    'examples' => ['drush steve_importAllSports' => 'Import Sports and tournaments']
  ];

  $commands['rp-sportandtournaments-api-importAllSportsbyPage'] = [
    'description' => 'Import Sports and tournaments by page',
    'callback' => 'rp_sportandtournaments_api_drush_importAllSports',
    'aliases' => ['steve_importAllSportsbyPage'],
    'examples' => ['drush steve_importAllSportsbyPage page ' => 'Import Sports and tournaments by page'],
    'arguments' => [
      'page' => 'page',
    ],
  ];

  $commands['rp-sportandtournaments-api-importAllSportbByID'] = [
    'description' => 'Import  Sports and tournaments by Sport/Tournament Steve Id',
    'callback' => 'rp_sportandtournaments_api_drush_importSportbByID',
    'aliases' => ['steve_importSportbByID'],
    'examples' => ['drush importAllSportbByID eventid' => 'Import  Sports and tournaments by Sport/Tournament Steve Id'],
    'arguments' => [
      'eventid' => 'eventid'
    ],
  ];

  $commands['rp-sportandtournaments-api-importAllSportsTranslations'] = [
    'description' => 'Import  Sports and tournaments Translation by Sport/Tournament Steve Id and lang',
    'callback' => 'rp_sportandtournaments_api_drush_importAllSportsTranslations',
    'aliases' => ['steve_importAllSportsTranslations'],
    'examples' => ['drush steve_importAllSportsTranslations eventid lang' => 'Import  Sports and tournaments Translation by Sport/Tournament Steve Id and lang'],
    'arguments' => [
      'lang' => 'lang',
      'page' => 'page',
    ],
  ];

  $commands['rp-sportandtournaments-api-importAllUpdateSportsTranslationByID'] = [
    'description' => 'Import all Sports and tournaments Translation by lang  and  page',
    'callback' => 'rp_sportandtournaments_api_drush_importUpdateSportsTranslationByID',
    'aliases' => ['steve_importAllUpdateSportsTranslationByID'],
    'examples' => ['drush steve_importAllUpdateSportsTranslationByID lang page' => 'Import all Sports and tournaments Translation by lang  and  page'],
    'arguments' => [
      'lang' => 'lang',
      'eventid' => 'eventid'
    ],
  ];

  return $commands;

}


/*----------------------------------------------------------------------------*/

function rp_sportandtournaments_api_drush_importAllSports($page = 0){
  $events = new generalController();
  return $events->sportsUpdateAllSports($page);
}

function rp_sportandtournaments_api_drush_importSportbByID($eventid){
  $events = new generalController();
  return $events->sportsUpdateSportbByID($eventid);
}

function rp_sportandtournaments_api_drush_importAllSportsTranslations($lang,$page){
  $events = new generalController();
  return $events->sportsUpdateAllSportsTranslations($lang,$page);
}

function rp_sportandtournaments_api_drush_importUpdateSportsTranslationByID($eventid,$lang){
  $events = new generalController();
   return $events->sportsUpdateSportsTranslationByID($lang , $eventid);
}

