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
  public $ChannelbyNode;

  /*
   * This function run a loop in the sport list - $listSport -
   * between two days ( - $parameters['start'] and $parameters['days'] )
   *
   * */
  public function importApiData() {
    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    $parametersList = $getInfoObj->getConfig();

    /*Creating Channels on Content Type  "Cannels"*/
    $creatorObj->createChannelsPages();

    foreach ($parametersList as $parameters) {
     /* $listSport = $parameters['sport'];
      if (!empty($listSport) and isset($listSport)) {
        foreach ($listSport as $sport_id) {
          $parameters['sport'] = $sport_id;
          $startday = $parameters['start'];
          for ($i = 0; $i < $parameters['days']; $i++) {
            $nuevafecha = strtotime('+' . $i . ' day', strtotime($startday));
            $date = date('Y-m-d', $nuevafecha);
            $parameters['start'] = $date;
            echo 'Import data from ' . $date . "\n";
            $this->Schedule($parameters);
            echo 'Import data from ' . $date . ' with Sport ID ' . $sport_id . ' . ' . "\n";
          }
        }
      }
      else {*/
        $startday = $parameters['start'];
        for ($i = 0; $i < $parameters['days']; $i++) {
          $nuevafecha = strtotime('+' . $i . ' day', strtotime($startday));
          $date = date('Y-m-d', $nuevafecha);
          $parameters['start'] = $date;
          echo 'Import data from ' . $date . "\n";
          $this->Schedule($parameters);
          echo 'Import all data from ' . $date . "\n";
        //}
      }
    }
    return TRUE;
  }

  /*
   *  Accessing to Schedule endpoint and creating related nodes.
   *
   * */

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
        if (isset($sportApiId)) { $sportTags = $creatorObj->createSportPages($sportDrupalId, $sportApiId, $region); }
        if (isset($streamProvider)) { $stream = $creatorObj->createStreamPages($streamProvider, $sportTags);}
        if (isset($competition)) { $creatorObj->createTournamentPages($competition, $sportTags, $sportApiId);}
        if (isset($participants)) { $Tags_Team = $creatorObj->createParticipantPages($participants, $sportTags);}
        if (isset($eventMeta)) { $ChannelbyNode = $getInfoObj->getIdChannelByNode($eventMeta);}
        $creatorObj->createGamePage($sportTags, $event, $stream, '', $Tags_Team, $ChannelbyNode, $region);
      }
      else {
        $node_id = reset($node)->id();
        $updateObj = new UpdateClienetData();
        $updNode = $updateObj->updateEvents($event, $node_id, $region);
        if ($updNode) {
          echo 'Updating Game Page "' . reset($node)->getTitle() . ' - at ' . date("h:i:s") . "\n";
          echo "\n";
        }
        /*$updateObj->updateTournament($event['competition']["id"], $node_id);
         $sportDrupalId = 'sport_' . $event['sport']['id'];
         $sportApiId = $event['sport']['id'];
         foreach ($event['participants'] as $participants) {
           $name = $participants['id'];
           $type = 'team_content';
           $opc = 'field_team_api_id';
           $participantsObj = $getInfoObj->getNode($name, $type, $opc);
           $participantsId = reset($participantsObj)->id();
           $updateObj->updateParticipant($participantsId);
         }
         if (isset($event['sport'])) {
           //Creating new sport page on CT "Sport Pages"
           $sportId = $event['sport']['id'];
           $sportTags = $creatorObj->createSportPages($sportDrupalId, $sportApiId);
         }*/

      }
    }
    echo "\n Schedule day(" . $parameters['start'] . ") has been updating at " . date("h:i:s") . " whit (" . count($allSchedule) . ") events.  \n\n";
    return TRUE;

  }
}
