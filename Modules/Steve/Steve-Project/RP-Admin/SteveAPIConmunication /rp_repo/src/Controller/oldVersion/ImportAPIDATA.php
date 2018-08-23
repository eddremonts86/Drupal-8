<?php

namespace Drupal\rp_repo\Controller\oldVersion;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\oldVersion\RepoGeneralGetInfo;
use Drupal\rp_repo\Controller\oldVersion\CreateClientData;
use Drupal\rp_repo\Controller\oldVersion\UpdateClienetData;
use Drupal\rp_repo\Controller\oldVersion\DeleteClientData;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;


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

  public static function create(ContainerInterface $container) {
    return parent::create($container); // TODO: Change the autogenerated stub
  }

  /*
   *  Accessing to Schedule endpoint and creating related nodes.
   */

  public function importApiData($date = '', $days = 1) {
    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();

    if ($date == null and $days == null ){
      $parametersList = $getInfoObj->getConfig();
    }
    else {
      $parametersList = $getInfoObj->getConfig($date, $days);
    }



    $creatorObj->createJsonLdTaxonomy();
    foreach ($parametersList as $parameters) {
        $this->Schedule($parameters);
    }
    // $this->makeNewsTraslations();
    return TRUE;
  }

  /*public function importApiBYDays($date, $days = 1) {
    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    $parametersList = $getInfoObj->getConfig($date, $days);
    $creatorObj->createJsonLdTaxonomy();
    foreach ($parametersList as $parameters) {
      $this->Schedule($parameters);
    }
    return TRUE;
  }

  public function importApiDataByDate($date = '') {

    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    $parametersList = $getInfoObj->getConfig($date, 1);
    /*Creating Channels on Content Type  "Cannels"
    $creatorObj->createChannelsPages();*
    $creatorObj->createJsonLdTaxonomy();
    foreach ($parametersList as $parameters) {
      $startday = $parameters['start'];
      for ($i = 0; $i < $parameters['days']; $i++) {
        $newDate = strtotime('+' . $i . ' day', strtotime($startday));
        $date = date('Y-m-d', $newDate);
        $parameters['start'] = $date;
        print 'Import data from ' . $date . "\n";
        $this->Schedule($parameters);
        print 'Import all data from ' . $date . "\n";
      }
    }
    // $this->makeNewsTraslations();

    return TRUE;
  }*/

  public function Schedule($parameters) {
    $rpClient = RPAPIClient::getClient(); //new guzzle http object
    $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    $tournament = new  taxonomyTournament();

    $allSchedule = $rpClient->getschedule($parameters); //get Schedule from API(JSON)
    $region = $getInfoObj->getClearUrl($parameters['region']);
    if ($region == "gb-eng") {$region = "en";}
    print "\n Schedule day(" . $parameters['start'] . ") whit (" . count($allSchedule) . ") events. Region(.'$region'.) \n\n";
    if (!empty($allSchedule)) {
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
          if (isset($competition)) {$tournamentID = $tournament->createTournamentAndParent($competition['id']);}
          if (isset($sportApiId)) {$sportTags = $tournament->getTaxonomyByOBj(['vid' => 'sport','field_api_id' => $sportDrupalId,], 1);}
          if (isset($streamProvider)) {$stream = $creatorObj->createStreamPages($streamProvider, $sportTags);}
          if (isset($participants)) {$Tags_Team = $creatorObj->createParticipantPages($participants, $sportTags, $parameters['langApiId']);}
          if (isset($eventMeta)) {$ChannelByNode = $getInfoObj->getIdChannelByNode($eventMeta);}
          $creatorObj->createGamePage($sportDrupalId, $event, $stream, '', $Tags_Team, $ChannelByNode, $region, $tournamentID);
        }
        else {
          $node_id = reset($node)->id();
          $updateObj = new UpdateClienetData();
          $updNode = $updateObj->updateEvents($event, $node_id, $region);
          if ($updNode) {
            print 'Updating Game Page - ' . reset($node)->getTitle(). "\n";
            print "\n";
          }
       }
      }
      return TRUE;
    }
    else {
      print "\n Schedule day(" . $parameters['start'] . ") is empty\n\n";
      return TRUE;

    }

  }

  public function makeNewsTraslations() {
    $test = new UpdateClienetData();
    $test->generateAliasbyTrasnlations();
    return TRUE;
  }

}
