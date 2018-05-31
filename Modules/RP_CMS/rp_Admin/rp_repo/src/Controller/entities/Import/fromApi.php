<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:48 PM
 */
namespace Drupal\rp_repo\Controller\entities\Import;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\entities\Generales\apiConfig;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyJsonLD;
use Drupal\rp_repo\Controller\entities\Taxonomies\TaxonomyChannel;
use Drupal\rp_repo\Controller\entities\Pages\Events;

class fromApi extends ControllerBase
{


  public function importApiData($date = '', $days = 0)
  {
    $getInfoObj = new apiConfig();
    if ($date != '' and $days == 0) {$parametersList = $getInfoObj->getConfig($date, 1);}
    elseif ($days != 0) {$parametersList = $getInfoObj->getConfig($date = 'Y-m-d', $days);}
    else {$parametersList = $getInfoObj->getConfig();}

    /*Creating Channels on Content Type  "Cannels"*/
    foreach ($parametersList as $parameters) {
      $startday = $parameters['start'];
      for ($i = 0; $i < $parameters['days']; $i++) {
        $newDate = strtotime('+' . $i . ' day', strtotime($startday));
        $date = date('Y-m-d', $newDate);
        $parameters['start'] = $date;
        echo 'Import data from ' . $date . "\n";
        $this->importSchedule($parameters);
        echo 'Import all data from ' . $date . "\n";
      }
    }
    return TRUE;
  }

  public function importSchedule($parameters)
  {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $allSchedule = $rpClient->getschedule($parameters); //get Schedule from API(JSON)
    $region = $parameters['region'];
    if ($region == "gb-eng") {
      $region = "en";
    }
    echo "\n Schedule day(" . $parameters['start'] . "). Update started at " . date("h:i:s") . " whit (" . count($allSchedule) . ") events.  \n\n";
    $getEvents = new Events();
    foreach ($allSchedule as $event) {
      $node = $getEvents->getNode($event['id'], 'events', 'field_event_api_id');
      if (!$node or empty($node)) {
        $sportDrupalId = 'sport_' . $event['sport']['id'];
        $sportApiId = $event['sport']['id'];
        $participants = $event['participants'];
        $competition = $event['competition'];
        $streamProvider = $event['streamprovider'];
        $eventMeta = $event['meta'];
        $Tags_Team = '';

        if (isset($sportApiId)) {
          $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
          $sportTags = $creatorObj->createSportPages($sportDrupalId, $sportApiId, $region, $color);
        }
        if (isset($streamProvider)) {
          $stream = $creatorObj->createStreamPages($streamProvider, $sportTags);
        }
        if (isset($competition)) {
          $creatorObj->createTournamentPages($competition, $sportTags, $sportApiId);
        }
        if (isset($participants)) {
          $Tags_Team = $creatorObj->createParticipantPages($participants, $sportTags);
        }
        if (isset($eventMeta)) {
          $ChannelByNode = $getInfoObj->getIdChannelByNode($eventMeta);
        }
        $creatorObj->createGamePage($sportTags, $event, $stream, '', $Tags_Team, $ChannelByNode, $region);
      } else {

        $node_id = reset($node)->id();
        $updateObj = new UpdateClienetData();
        $updNode = $updateObj->updateEvents($event, $node_id, $region);


        $sportDrupalId = 'sport_' . $event['sport']['id'];
        $sportApiId = $event['sport']['id'];
        if (isset($sportApiId)) {
          $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
          $sportTags = $creatorObj->createSportPages($sportDrupalId, $sportApiId, $region, $color);
        }

        if ($updNode) {
          echo 'Updating Game Page "' . reset($node)->getTitle() . ' - at ' . date("h:i:s") . "\n";
          echo "\n";
        }
      }
    }
    echo "\n Schedule day(" . $parameters['start'] . ") has been updating at " . date("h:i:s") . " whit (" . count($allSchedule) . ") events.  \n\n";
    return TRUE;

  }

  public function importChannels()
  {
      $tax = new TaxonomyChannel();
      $tax->createChannelsPages();
      return true;
  }

  public function importJsonLd()
  {
    $tax = new taxonomyJsonLD();
    $tax->createJsonLdTaxonomy();
    return true;
  }

}
