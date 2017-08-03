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
class ImportAPIDATA extends ControllerBase
{

    /*
     * This function run a loop in the sport list - $listSport -
     * between two days ( - $parameters['start'] and $parameters['days'] )
     *
     * */
    public function importApiData()
    {
        $creatorObj = new CreateClientData();
        $getInfoObj = new RepoGeneralGetInfo();
        $parameters = $getInfoObj->getConfig();
        $listSport = $parameters['sport'];

        if(!empty($listSport) and isset($listSport)){
            foreach ($listSport as $sport_id) {
                $creatorObj->createSportPages($sport_id);
                $parameters['sport'] = $sport_id;
                $startday = $parameters['start'];
                for ($i = 0; $i < $parameters['days']; $i++) {
                    $nuevafecha = strtotime('+' . $i . ' day',strtotime($startday));
                    $date = date('Y-m-d', $nuevafecha);
                    $parameters['start'] = $date;
                    echo 'Import data from ' . $date. "\n";
                    $this->Schedule($parameters);
                    echo 'Import data from ' . $date . ' with Sport ID ' . $sport_id . ' . ' . "\n";
                }
            }
        }
        else{
            $startday = $parameters['start'];
            for ($i = 0; $i < $parameters['days']; $i++) {
                $nuevafecha = strtotime('+' . $i . ' day',strtotime($startday));
                $date = date('Y-m-d', $nuevafecha);
                $parameters['start'] = $date;
                echo 'Import data from ' . $date. "\n";
                $this->Schedule($parameters);
                echo 'Import all data from ' . $date . "\n";
            }
        }

        return true;
    }

    /*
     *  Accessing to Schedule endpoint and creating related nodes.
     *
     * */

    public function Schedule($parameters)
    {
        $rpClient = RPAPIClient::getClient(); //new guzzlehttp object
        $creatorObj = new CreateClientData();
        $getInfoObj = new RepoGeneralGetInfo();
        $deletInfoObj = new DeleteClientData();

        $sportTags='';
        $stream='';
        $ChannelbyNode='';

        /*$isDone = $deletInfoObj->DeleteClientData();
        if($isDone){*/
            $creatorObj->createChannelsPages();  //Creating Channels on Content Type  "Cannels"
            $Allschedule = $rpClient->getschedule($parameters);
            for ($i = 0; $i < count($Allschedule); $i++) {
                $schedule = $Allschedule[$i];
                $node_id = '';
                $node = $getInfoObj->getNode($schedule['id'],'game_pages', 'field_game_pages_api_id');
                if (reset($node)) { $node_id = reset($node)->id();}
                if (!$node_id) {
                    $Metas = $creatorObj->createMeta($schedule);
                    if ($Metas) {
                        if (isset($schedule['sport'])) {
                            //Creating new sport page on CT "Sport Pages"
                            $sportTags = $creatorObj->createSportPages($schedule['sport']['id']);
                        }
                        if (isset($schedule['meta'])) {
                            $ChannelbyNode = $getInfoObj->getIdChannelByNode($schedule['meta']);
                        }
                        if (isset($schedule['streamprovider'])) {
                            $stream = $creatorObj->createStreamPages($schedule['streamprovider'],$sportTags);
                        }
                        $creatorObj->createTournamentPages($schedule['competition'],$sportTags,$schedule['sport']['id']);
                        $Tags_Team = $creatorObj->createParticipantPages($schedule['participants'],$sportTags);
                        $defautltText = $getInfoObj->getDefaultText($schedule['participants'][0]['name'],$schedule['participants'][1]['name']);
                        $creatorObj->createGamePage($sportTags, $schedule, $stream,$defautltText, $Tags_Team, $ChannelbyNode, $parameters);

                    }
                }
                else {
                    $node = reset($node);
                    $updateObj = new UpdateClienetData();
                    $updateObj->updateTournament($schedule['competition']["id"],$node_id);
                    foreach ($schedule['participants'] as $participants){
                        $name = $participants['id'];
                        $type = 'team_content';
                        $opc = 'field_team_api_id';
                        $participantsObj = $getInfoObj->getNode($name, $type,$opc);
                        $participantsId = reset($participantsObj)->id();
                        $updateObj->updateParticipant($participantsId);
                    }
                    if (isset($schedule['sport'])) {
                        //Creating new sport page on CT "Sport Pages"
                        $sportId = $schedule['sport']['id'];
                        $sportTags = $creatorObj->createSportPages($sportId);
                    }
                    $updNode = $updateObj->updateEvents($schedule , $node_id);
                    if($updNode){
                        echo 'Updating node "' . $node->getTitle() . "\n";
                        echo "\n";
                    }
                }
            }
            echo $parameters['start'] . "\n";
            return true;
   //     }
    }




}
