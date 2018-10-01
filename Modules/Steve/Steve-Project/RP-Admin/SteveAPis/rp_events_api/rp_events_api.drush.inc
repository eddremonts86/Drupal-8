<?php

use Drupal\rp_events_api\Controller\eventsAPIController;

/**
 * Implements hook_drush_command().
 */

function rp_events_api_drush_command() {

  $commands['rp-events-api-importAllEvents'] = [
    'description' => 'Import Events by date and page',
    'callback' => 'rp_events_api_drush_importAllEvents',
    'aliases' => ['steve_importAllEvents'],
    'examples' => ['drush steve_importAllEvents 2018/02/14 1' => 'Import Events by date and page'],
    'arguments' => [
      'date' => 'date',
      'page' => 'page',
    ],
  ];

  $commands['rp-events-api-importEventsByID'] = [
    'description' => 'Import Events by event id',
    'callback' => 'rp_events_api_drush_importEventsByID',
    'aliases' => ['steve_importEventsByID'],
    'examples' => ['drush steve_importEventsByID sport_2' => 'Import Events by event id'],
    'arguments' => [
      'Id' => 'Id'
    ],
  ];

  $commands['rp-events-api-importEventsRevisions'] = [
    'description' => 'Import  Events by event Steve API ID',
    'callback' => 'rp_events_api_drush_importEventsRevisions',
    'aliases' => ['steve_importEventsRevisions'],
    'examples' => ['drush steve_importEventsRevisions 5' => 'Import  Events by event Steve API ID'],
    'arguments' => [
      'Id' => 'Id'
    ],
  ];



  $commands['rp-events-api-importAllEventsTranslaion'] = [
    'description' => 'Import  Events Translation by date ,page and lang',
    'callback' => 'rp_events_api_drush_importAllEventsTranslaion',
    'aliases' => ['steve_importAllEventsTranslaion'],
    'examples' => ['drush steve_importAllEventsTranslaion 2018/02/14 2 da' => 'Import  Events Translation by date ,page and lang'],
    'arguments' => [
      'date' => 'date',
      'page' => 'page',
      'lang' => 'lang'
    ],
  ];

  $commands['rp-events-api-importEventsTranslaionByID'] = [
    'description' => 'Import Events Translation by event Steve API ID and lang',
    'callback' => 'rp_events_api_drush_importEventsTranslaionByID',
    'aliases' => ['steve_importEventsTranslaionByID'],
    'examples' => ['drush steve_importEventsTranslaionByID 2541 da' => 'Import Events Translation by event Steve API ID and lang'],
    'arguments' => [
      'Id' => 'Id',
      'lang' => 'lang'
    ],
  ];

  return $commands;

}

/*----------------------------------------------------------------------------*/

function rp_events_api_drush_importAllEvents($date , $page){
  $events = new eventsAPIController();
  return $events->getAll($date , $page);
}

function rp_events_api_drush_importEventsByID($id){
  $events = new eventsAPIController();
  return $events->geteventByID($id);
}

function rp_events_api_drush_importEventsRevisions($id){
  $events = new eventsAPIController();
  return $events->getLastEventRevisions($id);
}

function rp_events_api_drush_importAllEventsTranslaion($date , $page ,$lang){
  $events = new eventsAPIController();
  return $events->getAllTranslaion($date , $page, $lang );
}

function rp_events_api_drush_importEventsTranslaionByID($id,$lang){
  $events = new eventsAPIController();
  return $events->geteventTranslaionByID($id ,$lang);
}
