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
use Drupal\rp_rollbar\rollbarReport;


function rp_repo_drush_command() {
  /*-------------------------------------------- Get info from steve-api ----------------------------------------*/
  $commands['rp-repo-imporFromAPI'] = [
    'description' => 'Import schedule from Steve Api',
    'callback' => 'rp_repo_drush_importApiData',
    'aliases' => ['rp_importfromapi'],
    'examples' => [
      'drush rp_importfromapi' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importApiDataByDate'] = [
    'description' => 'Import schedule from Steve Api by date - 2018/05/12 - ',
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
    'description' => 'Import schedule from Steve Api by number of days ',
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
    'description' => 'Delete all  system entities',
    'callback' => 'rp_repo_drush_deleteAll',
    'aliases' => ['rp_deleteAll'],
    'examples' => [
      'drush rp_deleteAll' => 'Delete all  system entities',
    ],
  ];
  $commands['rp-repo-updateMainMenu'] = [
    'description' => 'Update clients Main Menu - used to hide a sport page with out events.',
    'callback' => 'rp_repo_drush_updateMainMenu',
    'aliases' => ['rp_updateMainMenu'],
    'examples' => [
      'drush rp_updateMainMenu' => 'To update Main Menu',
    ],
  ];
  $commands['rp-repo-updatePaht'] = [
    'description' => 'Recreate/update paht url for nodes and taxonomies',
    'callback' => 'rp_repo_drush_updatePaht',
    'aliases' => ['rp_updatePaht'],
    'examples' => [
      'drush rp_repo_drush_updatePaht' => 'Recreate/update paht ',
    ],
  ];
  $commands['rp-repo-importTournamentbyId'] = [
    'description' => 'Import all the dependencies tree for a tournament. Fron Tournament to Sport.',
    'callback' => 'rp_repo_drush_createTournamentAndParent',
    'aliases' => ['rp_createTournamentTree'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_createTournamentTree 10' => 'Import by id tournaments',
    ],
  ];
  $commands['rp-repo-importParticipantbyId'] = [
    'description' => 'Import Participant from Steve API by id ',
    'callback' => 'rp_repo_drush_createParticipantByID',
    'aliases' => ['rp_createParticipantByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_createParticipantByID 10' => 'Import by id',
    ],
  ];
  $commands['rp-repo-createStreamByID'] = [
    'description' => 'Import Streamer from Steve API by id ',
    'callback' => 'rp_repo_drush_createStreamByID',
    'aliases' => ['rp_createStreamByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush $rp_createStreamByID 10' => 'Import Streamer from Steve API by id ',
    ],
  ];
  $commands['rp-repo-importSchedule'] = [
    'description' => 'Delete and Re-import all the info of the system. All nodes and taxonomies and some of the basic configurations. This command is ONLY for developer env',
    'callback' => 'rp_repo_drush_importSchedule',
    'aliases' => ['rp_importSchedule'],
    'examples' => [
      'drush rp_importSchedule' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importSites'] = [
    'description' => 'Import all Sites from Steve Api',
    'callback' => 'rp_repo_drush_getsites',
    'aliases' => ['rp_importSites'],
    'examples' => [
      'drush rp_importSites' => 'Import all Sites from Steve Api',
    ],
  ];
  $commands['rp-repo-importSitesByID'] = [
    'description' => 'Import Site from Steve Api by id',
    'callback' => 'rp_repo_drush_getsitesBYID',
    'aliases' => ['rp_importSitesByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importSitesByID' => 'Import Site from Steve Api by id',
    ],
  ];
  $commands['rp-repo-importTimeZones'] = [
    'description' => ' Import Time Zones all from Steve Api ',
    'callback' => 'rp_repo_drush_gettimezone',
    'aliases' => ['rp_importTimeZones'],
    'examples' => [
      'drush rp_importTimeZones' => 'Import Time Zones all from Steve Api',
    ],
  ];
  $commands['rp-repo-importTimeZonesByID'] = [
    'description' => 'Import Time Zone from Steve Api by id  ',
    'callback' => 'rp_repo_drush_gettimezoneBYID',
    'aliases' => ['rp_importTimeZonesByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importTimeZonesByID' => 'Import Time Zone from Steve Api by id',
    ],
  ];
  $commands['rp-repo-importRegions'] = [
    'description' => 'Import all Regions from Steve Api  ',
    'callback' => 'rp_repo_drush_getregions',
    'aliases' => ['rp_importRegions'],
    'examples' => [
      'drush rp_importRegions' => 'Import all Regions from Steve Api',
    ],
  ];
  $commands['rp-repo-importRegionsByID'] = [
    'description' => 'Import Region from Steve Api by id  ',
    'callback' => 'rp_repo_drush_getregionsBYID',
    'aliases' => ['rp_importRegionsByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importRegionsByID' => 'Import Region from Steve Api by id',
    ],
  ];
  $commands['rp-repo-importlanguages'] = [
    'description' => 'Import all Languages from Steve Api  ',
    'callback' => 'rp_repo_drush_getlanguages',
    'aliases' => ['rp_importLanguages'],
    'examples' => [
      'drush rp_importLanguages' => 'Import all Languages from Steve Api',
    ],
  ];
  $commands['rp-repo-importlanguagesByID'] = [
    'description' => 'Import Language from Steve Api by id',
    'callback' => 'rp_repo_drush_getlanguagesBYID',
    'aliases' => ['rp_importLanguagesByID'],
    'arguments' => [
      'id' => 'id',
    ],
    'examples' => [
      'drush rp_importLanguagesByID' => 'Import Language from Steve Api by id ',
    ],
  ];
  $commands['rp-repo-importChannel'] = [
    'description' => 'Import all from Steve API',
    'callback' => 'rp_repo_drush_ImportChannels',
    'aliases' => ['rp_channels'],
    'examples' => [
      'drush rp_channels' => 'Import all from Steve API',
    ],
  ];
  $commands['rp-repo-importJSONLD'] = [
    'description' => 'Create and import a JSON-LD config Taxonomy',
    'callback' => 'rp_repo_drush_importJSONLD',
    'aliases' => ['rp_jsonld'],
    'examples' => [
      'drush rp_jsonld' => 'Create and import a JSON-LD config Taxonomy',
    ],
  ];
  return $commands;

  /*--------------------------------------------Delete ---------------------------------------------------
  $commands['rp-repo-importScheduleByDate'] = [
    'description' => '  ',
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
    'description' => '  ',
    'callback' => 'rp_repo_drush_importApiBYDays',
    'aliases' => ['rp_importApiBYDays'],
    'arguments' => [
      'days' => 'Days',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
    ],
  ];
  $commands['rp-repo-importUser'] = [
    'description' => '  ',
    'callback' => 'rp_repo_drush_getAllUsers',
    'aliases' => ['$rp_getAllUsers'],
    'examples' => [
      'drush $rp_importSchedule' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importUserbyLocalSite'] = [
    'description' => '  ',
    'callback' => 'rp_repo_drush_getUserbyLocalSite',
    'aliases' => ['$rp_getUserbyLocalSite'],
    'examples' => [
      'drush $rp_importSchedule' => 'Import all data',
    ],
  ];
  $commands['rp-repo-importUserbySite'] = [
    'description' => '  ',
    'callback' => 'rp_repo_drush_getUserbySite',
    'aliases' => ['$rp_getUserbySite'],
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

  /*-----------------------------------------------------------------------------------------------*/


}

/*Old version*/

function rp_repo_drush_importApiData() {
  $obj = new ImportAPIDATA();
  $obj->importApiData(null,null);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
  rp_repo_drush_updateMainMenu();
  return true;

}

function rp_repo_drush_importApiDataByDate($date) {
  $obj = new ImportAPIDATA();
  $data = $obj->importApiData($date , 1);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
  rp_repo_drush_updateMainMenu();
  return true;
}

function rp_repo_drush_importApiBYDays($days) {
  $obj = new ImportAPIDATA();
  $obj->importApiData($date = 'Y-m-d', $days);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
  rp_repo_drush_updateMainMenu();
  return true;
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
  return true;

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
  return true;
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
  return true;
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

  drush_print('Cleaning cache.');
  drupal_flush_all_caches();
  drush_print('Success.');
  return TRUE;

}

function rp_repo_drush_updatePaht() {

}

function rp_repo_drush_createTournamentAndParent($id) {
  $api = new taxonomyTournament();
  $api->createTournamentAndParent($id);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
  return true;
}

function rp_repo_drush_createParticipantByID($id) {
  $api = new taxonomyParticipan();
  $api->createParticipantByID($id);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
  return true;
}

function rp_repo_drush_createStreamByID($id) {
  $api = new taxonomyStream();
  $api->createStreamByID($id);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
  return true;
}

function rp_repo_drush_getlanguages() {
  $site = new taxonomySteveLanguages();
  $site->importLanguages();
  drush_print('Done....');
  return true;

}

function rp_repo_drush_getregions() {
  $site = new taxonomySteveRegion();
  $site->importRegions();
  drush_print('Done....');
  return true;

}

function rp_repo_drush_gettimezone() {
  $site = new taxonomySteveTimeZone();
  $site->importTimeZones();
  drush_print('Done....');
  return true;
}

function rp_repo_drush_getsites() {
  $site = new taxonomySteveSite();
  $site->importSites();
  drush_print('Done....');
  return true;
}

function rp_repo_drush_getlanguagesBYID($api_id) {
  $site = new taxonomySteveLanguages();
  $site->importLanguage($api_id);
  drush_print('Done....');
  return true;

}

function rp_repo_drush_getregionsBYID($id) {
  $site = new taxonomySteveRegion();
  $site->importRegion($id);
  drush_print('Done....');
  return true;

}

function rp_repo_drush_gettimezoneBYID($id) {
  $site = new taxonomySteveTimeZone();
  $site->importTimeZone($id);
  drush_print('Done....');
  return true;
}

function rp_repo_drush_getsitesBYID($id) {
  $site = new taxonomySteveSite();
  $site->importSite($id);
  drush_print('Done....');
  return true;
}

function rp_repo_drush_getAllUsers() {
  $site = new Users();
  $site->getuser();
  drush_print('Done....');return true;
}

function rp_repo_drush_getUserbySite($siteId) {
  $site = new Users();
  $site->getuserbySiteid($siteId);
  drush_print('Done....');return true;
}

function rp_repo_drush_getUserbyLocalSite() {
  $site = new Users();
  $site->getuserbySiteid(NULL);
  drush_print('Done....');return true;
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
  drush_print('Success.');return true;
}



