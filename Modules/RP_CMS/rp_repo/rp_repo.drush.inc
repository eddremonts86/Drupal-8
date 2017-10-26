<?php

use Drupal\rp_repo\Controller\ImportRepoData;
use Drupal\rp_repo\Controller\ImportAPIDATA;
use Drupal\rp_repo\Controller\DeleteClientData;
use Drupal\rp_repo\Controller\UpdateClienetData;


function rp_repo_drush_command() {
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
    'aliases' => ['rp_importApiBYDays'],
    'arguments' => [
      'days' => 'Days',
    ],
    'examples' => [
      'drush rp_importApiBYDays 10' => 'Import all data for the next 10 days',
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

  return $commands;
}

function rp_repo_drush_importApiData() {
  $obj = new ImportAPIDATA();
  $data = $obj->importApiData();
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_importApiDataByDate($date) {
  $obj = new ImportAPIDATA();
  $data = $obj->importApiData($date);
  drush_print('Import data from API(http://steve.rebelpenguin.dk).');
  drush_print('Success.');
}

function rp_repo_drush_importApiBYDays($days) {
  $obj = new ImportAPIDATA();
  $data = $obj->importApiData($date = 'Y-m-d', $days);
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
  $obj = new ImportRepoData();
  $data = $obj->getSchedule(5, $id);
  if (!empty($data)) {
    var_dump($data);
  }
  else {
    drush_print('We had a problem.');
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

function rp_repo_drush_updateMainMenu(){
  $obj = new UpdateClienetData();
  $data = $obj->updateMainMenu();
  if ($data) {
    drush_print('Main Menu has beeen updated');
    drush_print('Success.');
  }
  else {
    drush_print('We had a problem.');
  }
}


?>