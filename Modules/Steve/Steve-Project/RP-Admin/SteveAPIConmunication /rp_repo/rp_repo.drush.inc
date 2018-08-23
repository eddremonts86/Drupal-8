<?php
use Drupal\rp_repo\Controller\oldVersion\ImportRepoData;
use Drupal\rp_repo\Controller\oldVersion\ImportAPIDATA;
use Drupal\rp_repo\Controller\oldVersion\DeleteClientData;
use Drupal\rp_repo\Controller\oldVersion\UpdateClienetData;
use Drupal\rp_repo\Controller\entities\Import\fromApi;
use Drupal\rp_repo\Controller\entities\System\Users;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveTimeZone;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveRegion;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveLanguages;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyStream;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyParticipan;

function rp_repo_drush_command() {
  /*-------------------------------------------- Get info from steve-api ----------------------------------------*/
  $commands['rp-repo-imporFromAPI'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_importApiData',
    'aliases' => ['rp_importfromapi'],
    'examples' => [
      'drush rp_importfromapi' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importApiDataByDate'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_importApiDataByDate',
    'aliases' => ['rp_importApiDataByDate'],
    'arguments' => [
      'date' => 'Date',
    ],
    'examples' => [
      'drush rp_importApiDataByDate 2015-12-25' => 'Import all data for this day',
    ],
  ];
  $commands['rp-repo-importApiBYDays'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_importApiBYDays',
    'aliases' => ['rp_importApiByDays'],
    'arguments' => [
      'days' => 'Days',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-deleteAll'] = [
    'description' => '-==- Import data and Update all content type and tax From REPO  -==- ',
    'callback' => 'rp_repo_drush_deleteAll',
    'aliases' => ['rp_deleteAll'],
    'examples' => [
      'drush rp_deleteAll' => 'Import data and Update data',
    ],
  ];
  $commands['rp-repo-updateMainMenu'] = [
    'description' => '-==- To update Main Menu, take out sport with out future Events  -==- ',
    'callback' => 'rp_repo_drush_updateMainMenu',
    'aliases' => ['rp_updateMainMenu'],
    'examples' => [
      'drush rp_updateMainMenu' => 'To update Main Menu',
    ],
  ];
  $commands['rp-repo-updatePaht'] = [
    'description' => '-==- To update Main Menu, take out sport with out future Events  -==- ',
    'callback' => 'rp_repo_drush_updatePaht',
    'aliases' => ['rp_updatePaht'],
    'examples' => [
      'drush rp_updateMainMenu' => 'To update Main Menu',
    ],
  ];
  $commands['rp-repo-importTournamentbyId'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_createTournamentAndParent',
    'aliases' => ['steve_createTournamentTree'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importParticipantbyId'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_createParticipantByID',
    'aliases' => ['steve_createParticipantByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-createStreamByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_createStreamByID',
    'aliases' => ['steve_createStreamByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importSchedule'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_importSchedule',
    'aliases' => ['steve_importSchedule'],
    'examples' => [
      'drush steve_importSchedule' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importScheduleByDate'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_importApiDataByDate',
    'aliases' => ['rp_importApiDataByDate'],
    'arguments' => [
      'date' => 'Date',
    ],
    'examples' => [
      'drush rp_importApiDataByDate 2015-12-25' => 'Import all data for this day',
    ],
  ];
  $commands['rp-repo-importScheduleBYDays'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_importApiBYDays',
    'aliases' => ['rp_importApiBYDays'],
    'arguments' => [
      'days' => 'Days',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importSites'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getsites',
    'aliases' => ['rp_importSites'],
    'examples' => [
      'drush rp_importSites' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importSitesByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getsitesBYID',
    'aliases' => ['rp_importSitesByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importSites' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importTimeZones'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_gettimezone',
    'aliases' => ['rp_importTimeZones'],
    'examples' => [
      'drush rp_TimeZones' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importTimeZonesByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_gettimezoneBYID',
    'aliases' => ['rp_importTimeZonesByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_TimeZones' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importRegions'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getregions',
    'aliases' => ['rp_importRegions'],
    'examples' => [
      'drush rp_TimeZones' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importRegionsByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getregionsBYID',
    'aliases' => ['rp_importRegionsByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_TimeZones' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importlanguages'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getlanguages',
    'aliases' => ['rp_importLanguages'],
    'examples' => [
      'drush rp_languages' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importlanguagesByID'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getlanguagesBYID',
    'aliases' => ['rp_importLanguagesByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_languages' => 'Import all data for the next 10 days',
    ],
  ];
  /*-------------------------------------------- Get info from Drupal-steve-api ----------------------------------------*/
  $commands['rp-repo-importChannel'] = [
    'description' => '-==- To update Main Menu, take out sport with out future Events  -==- ',
    'callback' => 'rp_repo_drush_ImportChannels',
    'aliases' => ['steve_channels'],
    'examples' => [
      'drush rp_updateMainMenu' => 'To update Main Menu',
    ],
  ];
  $commands['rp-repo-importJSONLD'] = [
    'description' => '-==- To update Main Menu, take out sport with out future Events  -==- ',
    'callback' => 'rp_repo_drush_importJSONLD',
    'aliases' => ['steve_jsonld'],
    'examples' => [
      'drush rp_updateMainMenu' => 'To update Main Menu',
    ],
  ];
  $commands['rp-repo-importUser'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getAllUsers',
    'aliases' => ['steve_getAllUsers'],
    'examples' => [
      'drush steve_importSchedule' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importUserbyLocalSite'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getUserbyLocalSite',
    'aliases' => ['steve_getUserbyLocalSite'],
    'examples' => [
      'drush steve_importSchedule' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importUserbySite'] = [
    'description' => '-==- Import data and create all content type and tax From API -==-  ',
    'callback' => 'rp_repo_drush_getUserbySite',
    'aliases' => ['steve_getUserbySite'],
    'arguments' => [
      'siteId' => 'siteId',
    ],
    'examples' => [
      'drush rp_importApiDataByDate 2015-12-25' => 'Import all data for this day',
    ],
  ];
  $commands['rp-repo-imporFromContRepo'] = [
    'description' => '-==- Import data and Update all content type and tax From REPO  -==- ',
    'callback' => 'rp_repo_drush_importRepoData',
    'aliases' => ['rp_importfromrepo'],
    'examples' => [
      'drush rp_importfromrepo' => 'Import data and Update data',
    ],
  ];
  $commands['rp-repo-getSchedule'] = [
    'description' => '-==- Get all Schedule (Just for test) -==- ',
    'callback' => 'rp_repo_drush_getSchedule',
    'aliases' => ['rp_getschedule'],
    'arguments' => [
      'id' => 'Entity',
    ],
    'examples' => [
      'drush rp_getSchedule' => 'Import data and Update data',
    ],
  ];
  return $commands;
}

/*Old version*/

function rp_repo_drush_importApiData() {
  $obj = new ImportAPIDATA();
  $obj->importApiData(null,null);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_importApiDataByDate($date) {
  $obj = new ImportAPIDATA();
  $data = $obj->importApiData($date , 1);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_importApiBYDays($days) {
  $obj = new ImportAPIDATA();
  $obj->importApiData($date = 'Y-m-d', $days);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_importRepoData() {
  $obj = new ImportRepoData();
  $data = $obj->importRepoData();
  if (!empty($data)) {
    drush_print('Import data from REPO(http://www.cmsrepo.rebelpenguin.dk).');
    drush_print('Success.');
  }
  else {
    drush_print('We had a problem.');
  }


}

function rp_repo_drush_getSchedule($id) {
  if (isset($id)) {
    $obj = new ImportRepoData();
    $data = $obj->getSchedule(5, $id);
    if (!empty($data)) {
      drush_print("\n" . 'At least ' . count($data) . ' events');
    }
    else {
      drush_print("\n" . 'We had a problem.');
    }
  }
  else {
    drush_print("\n" . 'We had a problem. We need a sport api id.');
  }
}

function rp_repo_drush_deleteAll() {
  $obj = new DeleteClientData();
  $data = $obj->DeleteClientData();
  if ($data) {
    drush_print('Delete data from client.');
    drush_print('Success.');
  }
  else {
    drush_print('We had a problem.');
  }

}

function rp_repo_drush_updateMainMenu() {
  $obj = new UpdateClienetData();
  $data = $obj->updateMainMenu();
  if ($data) {
    drush_print('Main Menu has beeen updated');
    drush_print('Success.');
  }
  else {
    drush_print('We had a problem.');
  }

  $obj = new DeleteClientData();
  $data = $obj->desableEvents();
  if ($data) {
    drush_print('Events has beeen updated');
    drush_print('Success.');
  }
  else {
    drush_print('We had a problem.');
  }


}

function rp_repo_drush_updatePaht() {

}



function rp_repo_drush_createTournamentAndParent($id) {
  $api = new taxonomyTournament();
  $api->createTournamentAndParent($id);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_createParticipantByID($id) {
  $api = new taxonomyParticipan();
  $api->createParticipantByID($id);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_createStreamByID($id) {
  $api = new taxonomyStream();
  $api->createStreamByID($id);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_getlanguages() {
  $site = new taxonomySteveLanguages();
  $site->importLanguages();
  drush_print('Done....');

}

function rp_repo_drush_getregions() {
  $site = new taxonomySteveRegion();
  $site->importRegions();
  drush_print('Done....');

}

function rp_repo_drush_gettimezone() {
  $site = new taxonomySteveTimeZone();
  $site->importTimeZones();
  drush_print('Done....');
}

function rp_repo_drush_getsites() {
  $site = new taxonomySteveSite();
  $site->importSites();
  drush_print('Done....');
}


function rp_repo_drush_getlanguagesBYID($api_id) {
  $site = new taxonomySteveLanguages();
  $site->importLanguage($api_id);
  drush_print('Done....');

}

function rp_repo_drush_getregionsBYID($id) {
  $site = new taxonomySteveRegion();
  $site->importRegion($id);
  drush_print('Done....');

}

function rp_repo_drush_gettimezoneBYID($id) {
  $site = new taxonomySteveTimeZone();
  $site->importTimeZone($id);
  drush_print('Done....');
}

function rp_repo_drush_getsitesBYID($id) {
  $site = new taxonomySteveSite();
  $site->importSite($id);
  drush_print('Done....');
}



function rp_repo_drush_getAllUsers() {
  $site = new Users();
  $site->getuser();
  drush_print('Done....');
}

function rp_repo_drush_getUserbySite($siteId) {
  $site = new Users();
  $site->getuserbySiteid($siteId);
  drush_print('Done....');
}

function rp_repo_drush_getUserbyLocalSite() {
  $site = new Users();
  $site->getuserbySiteid(NULL);
  drush_print('Done....');
}

function rp_repo_drush_ImportChannels() {
  $api = new fromApi();
  $api->importChannels();
  return TRUE;
}

function rp_repo_drush_importJSONLD() {
  $api = new fromApi();
  $api->importJsonLd();
  return TRUE;
}

function rp_repo_drush_importSchedule() {
  /*--------------- dev ----------------*/
  $del = new DeleteClientData();
  $del->DeleteClientData();
  $site = new taxonomySteveSite();
  $config = \Drupal::configFactory()->get('rp_base.settings');
  $site_api_id = $config->get('rp_base_site_api_id');
  $site->importSite($site_api_id);
  /*-----------------------------------*/

  $obj = new ImportAPIDATA();
  $obj->importApiData('Y-m-d');

  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}



