<?php

use Drupal\rp_repo\Controller\oldVersion\ImportRepoData;
use Drupal\rp_repo\Controller\oldVersion\ImportAPIDATA;
use Drupal\rp_repo\Controller\oldVersion\DeleteClientData;
use Drupal\rp_repo\Controller\oldVersion\UpdateClienetData;


/* new */

use Drupal\rp_repo\Controller\entities\Import\fromApi;
use Drupal\rp_repo\Controller\entities\Import\fromRepo;


use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyStream;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyParticipan;

/*
    RP Drush command
 * rp_importfromapi
 * rp_importApiDataByDate
 * rp_importApiBYDays
 * rp_importfromrepo
 * rp_getschedule
 * rp_deleteAll
 * rp_updateMainMenu
 * rp_updatePaht
 *
 * composer require guzzlehttp/guzzle-services
   composer require cocur/slugify
 *
 * */


function rp_repo_drush_command()
{
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
    $commands['rp-repo-updatePaht'] = [
        'description' => '-==- To update Main Menu, take out sport with out future Events  -==- ',
        'callback' => 'rp_repo_drush_updatePaht',
        'aliases' => ['rp_updatePaht'],
        'examples' => [
            'drush rp_updateMainMenu' => 'To update Main Menu',
        ],
    ];


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
    $commands['rp-repo-importPArticipantbyId'] = [
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

    return $commands;
}


/*Old version*/

function rp_repo_drush_importApiData()
{
    $obj = new ImportAPIDATA();
    $data = $obj->importApiData();
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

function rp_repo_drush_importApiDataByDate($date)
{
    $obj = new ImportAPIDATA();
    $data = $obj->importApiDataByDate($date);
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

function rp_repo_drush_importApiBYDays($days)
{
    $obj = new ImportAPIDATA();
    $data = $obj->importApiBYDays($date = 'Y-m-d', $days);
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

function rp_repo_drush_importRepoData()
{
    $obj = new ImportRepoData();
    $data = $obj->importRepoData();
    if (!empty($data)) {
        drush_print('Import data from REPO(http://www.cmsrepo.rebelpenguin.dk).');
        drush_print('Success.');
    } else {
        drush_print('We had a problem.');
    }


}

function rp_repo_drush_getSchedule($id)
{
    if (isset($id)) {
        $obj = new ImportRepoData();
        $data = $obj->getSchedule(5, $id);
        if (!empty($data)) {
            drush_print("\n" . 'At least ' . count($data) . ' events');
        } else {
            drush_print("\n" . 'We had a problem.');
        }
    } else {
        drush_print("\n" . 'We had a problem. We need a sport api id.');
    }
}

function rp_repo_drush_deleteAll()
{
    $obj = new DeleteClientData();
    $data = $obj->DeleteClientData();
    if ($data) {
        drush_print('Delete data from client.');
        drush_print('Success.');
    } else {
        drush_print('We had a problem.');
    }

}

function rp_repo_drush_updateMainMenu()
{
    $obj = new UpdateClienetData();
    $data = $obj->updateMainMenu();
    if ($data) {
        drush_print('Main Menu has beeen updated');
        drush_print('Success.');
    } else {
        drush_print('We had a problem.');
    }

    $obj = new DeleteClientData();
    $data = $obj->desableEvents();
    if ($data) {
        drush_print('Events has beeen updated');
        drush_print('Success.');
    } else {
        drush_print('We had a problem.');
    }


}

function rp_repo_drush_updatePaht()
{

}


/* New Version */

function rp_repo_drush_ImportChannels()
{
    $api = new fromApi();
    $api->importChannels();
    return true;
}

function rp_repo_drush_importJSONLD()
{
    $api = new fromApi();
    $api->importJsonLd();
    return true;
}

function rp_repo_drush_importSchedule()
{
    $api = new fromApi();
    $api->importApiData();
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

function rp_repo_drush_createTournamentAndParent($id)
{
    $api = new taxonomyTournament();
    $api->createTournamentAndParent($id);
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

function rp_repo_drush_createParticipantByID($id)
{
    $api = new taxonomyParticipan();
    $api->createParticipantByID($id);
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

function rp_repo_drush_createStreamByID($id)
{
    $api = new taxonomyStream();
    $api->createStreamByID($id);
    drush_print('Import data from API(http://steve.rebelpenguin.dk).');
    drush_print('Success.');
}

?>