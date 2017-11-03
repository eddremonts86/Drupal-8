<?php

namespace Drupal\rp_repo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\RepoGeneralGetInfo;
use Drupal\rp_repo\Controller\CreateClientData;
use Drupal\rp_repo\Controller\UpdateClienetData;
use Drupal\rp_repo\Controller\DeleteClientData;


/**
 * Class ImportAPIDATA.
 *
 * @package Drupal\rp_repo\Controller
 */
class ImportAPIDATA extends ControllerBase {


  public $sportTags;
  public $stream;
  public $ChannelByNode;

  /*
   * This function run a loop in the sport list - $listSport -
   * between two days ( - $parameters['start'] and $parameters['days'] )
   *
   */

  public function importApiData($date = '', $days = 0 ) {
    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    if($date != '' and $days == 0  ){$parametersList = $getInfoObj->getConfig($date,1);}
    elseif ($days != 0 ){$parametersList = $getInfoObj->getConfig($date = 'Y-m-d', $days);}
    else {$parametersList = $getInfoObj->getConfig();}
    /*Creating Channels on Content Type  "Cannels"*/
    $creatorObj->createChannelsPages();
    foreach ($parametersList as $parameters) {
        $startday = $parameters['start'];
        for ($i = 0; $i < $parameters['days']; $i++) {
          $nuevafecha = strtotime('+' . $i . ' day', strtotime($startday));
          $date = date('Y-m-d', $nuevafecha);
          $parameters['start'] = $date;
          echo 'Import data from ' . $date . "\n";
          $this->Schedule($parameters);
          echo 'Import all data from ' . $date . "\n";
      }
    }
    return TRUE;
  }

  /*
   *  Accessing to Schedule endpoint and creating related nodes.
   *
   *
   */

  public function Schedule($parameters) {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    $allSchedule = $rpClient->getschedule($parameters); //get Schedule from API(JSON)
    $region = $getInfoObj->getClearUrl($parameters['region']);
    echo "\n Schedule day(" . $parameters['start'] . "). Update started at " . date("h:i:s") . " whit (" . count($allSchedule) . ") events.  \n\n";
    foreach ($allSchedule as $event) {
      $node = $getInfoObj->getNode($event['id'], 'events', 'field_event_api_id');
      if (!$node or empty($node)) {
        $sportDrupalId = 'sport_' . $event['sport']['id'];
        $sportApiId = $event['sport']['id'];
        $participants = $event['participants'];
        $competition = $event['competition'];
        $streamProvider = $event['streamprovider'];
        $eventMeta = $event['meta'];
        $Tags_Team = '';

        var_dump("1");if (isset($sportApiId)) { $sportTags = $creatorObj->createSportPages($sportDrupalId, $sportApiId, $region); }
        var_dump("2");if (isset($streamProvider)) { $stream = $creatorObj->createStreamPages($streamProvider, $sportTags);}
        var_dump("3");if (isset($competition)) { $creatorObj->createTournamentPages($competition, $sportTags, $sportApiId);}
        var_dump("4");if (isset($participants)) { $Tags_Team = $creatorObj->createParticipantPages($participants, $sportTags);}
        var_dump("5");if (isset($eventMeta)) { $ChannelByNode = $getInfoObj->getIdChannelByNode($eventMeta);}
        var_dump("6");$creatorObj->createGamePage($sportTags, $event, $stream, '', $Tags_Team, $ChannelByNode, $region);
      }
      else {
        $node_id = reset($node)->id();
        $updateObj = new UpdateClienetData();
        $updNode = $updateObj->updateEvents($event, $node_id, $region);
        if ($updNode) {
          echo 'Updating Game Page "' . reset($node)->getTitle() . ' - at ' . date("h:i:s") . "\n";
          echo "\n";
        }
      }
    }
    echo "\n Schedule day(" . $parameters['start'] . ") has been updating at " . date("h:i:s") . " whit (" . count($allSchedule) . ") events.  \n\n";
    return TRUE;

  }
}
