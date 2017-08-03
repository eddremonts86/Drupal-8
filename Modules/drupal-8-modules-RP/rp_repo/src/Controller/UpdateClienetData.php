<?php

namespace Drupal\rp_repo\Controller;


use Drupal\Core\Controller\ControllerBase;


use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\RepoGeneralGetInfo;

/**
 * Class UpdateClienetData.
 */
class UpdateClienetData extends ControllerBase
{

    public function updatePathauto()
    {

    }

    public function updateParticipant($nodeId)
    {
        $getObj = new RepoGeneralGetInfo();
        $rpClient = RPAPIClient::getClient();
        $parameters = ['id' => $nodeId];
        $Participants = $rpClient->getParticipantsbyID($parameters);
        $logo_modified = $Participants[0]["logo_modified"];
        $ParticipantsObj = Node::load($nodeId);
        $name = $Participants[0]["name"];
        $logoModified = strtotime($logo_modified);
        $fieldLogoDate=  strtotime($ParticipantsObj->field_logo_participant_date->value);

        if($logoModified != $fieldLogoDate){
            $logo_path = $Participants[0]["logo_path"];
            $logo = $getObj->getImg($logo_path,$getObj->getClearUrl( $name. '_logo'));
            $ParticipantsObj->field_participant_logo = $logo;
            $ParticipantsObj->field_logo_participant_date = $logo_modified;
            $ParticipantsObj->save();
            echo 'Update Participant -  '.$name. ' - ' . "\n";
        }
        return true;
    }

    public function updateTournament($competitionId)
    {
        $rpClient = RPAPIClient::getClient();
        $parameters = ['id' => $competitionId];
        $competition = $rpClient->getCompetitionsbyID($parameters);
        $logo_path = $competition[0]["logo_path"];
        $logo_modified = $competition[0]["logo_modified"];
        $competitionName = $competition[0]["name"];

        $getObj = new RepoGeneralGetInfo();
        $node = $getObj->getNode($competitionId, 'tournament_page','field_tournament_api_id');
        $node = reset($node);
        if (strtotime($logo_modified) != strtotime($node->field_logo_date->value)) {
            $newLogo = $getObj->getImg($logo_path, $competitionName . '_logo');
            $node->title = $competitionName;
            $node->field_logo_date = $logo_modified;
            $node->field_logo = $newLogo;
            $node->save();
            echo 'Update Tournament -' . $competitionName . ' - ' . "\n";
        }
        return $competitionId;
    }

    public function updateEvents($event,$node_id)
    {
        $creatorObj = new CreateClientData();
        $getInfoObj = new RepoGeneralGetInfo();

        $node = Node::load($node_id);
        $sportTags = $getInfoObj->getTaxonomyByAPIID($event["sport"]["id"]);

        //Update General Event Meta
        if (isset($event['meta'])) {
            $ChannelbyNode = $getInfoObj->getIdChannelByNode($event['meta']);
            $node->field_game_channels_ref = $ChannelbyNode;

        }
        if (isset($event['streamprovider'])) {
            $stream = $creatorObj->createStreamPages($event['streamprovider'],$sportTags->id());
            $node->field_stream_provider_gp = $stream;
        }
        if (isset($event['streamprovider'])) {
            $Tags_Team = $creatorObj->createParticipantPages($event['participants'],$sportTags);
            $node->field_game_participants_tax = $Tags_Team;
        }
        $node->field_game_date = strtotime($event['start']);
        $node->save();
        return true;

    }

    public function updateChanel($nodeId,$channel)
    {
        $node = Node::load($nodeId);
        $changed = false;
        if(isset($node)) {
            if ($node->field_channel_description->value‎ != $channel['description'] and $node->field_channel_description‎ != null) {
                $node->field_channel_description‎ = $channel['description'];
                $changed = true;
            }
            if ($node->field_channel_notes != $channel['notes'] and $node->field_channel_notes != null) {
                $node->field_channel_notes = $channel['notes'];
                $changed = true;
            }
            if ($changed == true) {
                $node->save();
                echo 'Updating node (Channels) "' . $node->getTitle() . "\n";
            }
        }
        return true;
    }

    public function updateMetaByChanel()
    {
    }

    public function UpdateClienetData()
    {
    }

}
