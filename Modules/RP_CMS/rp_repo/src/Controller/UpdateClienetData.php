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
class UpdateClienetData extends ControllerBase {

  public function updatePathauto() {

  }

  public function updateParticipant($nodeId) {
    $getObj = new RepoGeneralGetInfo();
    $rpClient = RPAPIClient::getClient();
    $parameters = ['id' => $nodeId];
    $Participants = $rpClient->getParticipantsbyID($parameters);
    $logo_modified = $Participants[0]["logo_modified"];
    $ParticipantsObj = Node::load($nodeId);
    $name = $Participants[0]["name"];
    $logoModified = strtotime($logo_modified);
    $fieldLogoDate = strtotime($ParticipantsObj->field_logo_participant_date->value);

    if ($logoModified != $fieldLogoDate) {
      $logo_path = $Participants[0]["logo_path"];
      $logo = $getObj->getImg($logo_path, $getObj->getClearUrl($name . '_logo'));
      $ParticipantsObj->field_participant_logo = $logo;
      $ParticipantsObj->field_logo_participant_date = $logo_modified;
      $ParticipantsObj->save();
      echo 'Update Participant -  ' . $name . ' - at ' . date("h:i:s") . "\n";
    }
    return TRUE;
  }

  public function updateTournament($competitionId) {
    $rpClient = RPAPIClient::getClient();
    $parameters = ['id' => $competitionId];
    $competition = $rpClient->getCompetitionsbyID($parameters);
    $logo_path = $competition[0]["logo_path"];
    $logo_modified = $competition[0]["logo_modified"];
    $competitionName = $competition[0]["name"];
    $getObj = new RepoGeneralGetInfo();
    $node = $getObj->getNode($competitionId, 'tournament_page', 'field_tournament_api_id');
    $node = reset($node);
    if (strtotime($logo_modified) != strtotime($node->field_logo_date->value)) {
      $newLogo = $getObj->getImg($logo_path, $competitionName . '_logo');
      $node->title = $competitionName;
      $node->field_logo_date = $logo_modified;
      $node->field_logo = $newLogo;
      $node->save();
      echo 'Update Tournament -' . $competitionName . ' - at ' . date("h:i:s") . "\n";
    }
    return $competitionId;
  }

  public function updateEvents($event, $node_id, $region) {
   // $creatorObj = new CreateClientData();
    $getInfoObj = new RepoGeneralGetInfo();
    $node = Node::load($node_id);
    $sport = $getInfoObj->getTaxonomyByID($node->field_event_sport->target_id);
    $alias = '/' . $getInfoObj->getClearUrl(reset($sport)["name"]["x-default"]) . '/' . $region . '/' . $getInfoObj->getClearUrl($event['name']);
    $ifAlias = $getInfoObj->getAlias('/node/'.$node_id, $alias);
    $alias = ['alias' => $alias];
    if(empty($ifAlias) or !$ifAlias) {
      $node->path = $alias;
      $node->field_game_date = strtotime($event['start']);
      $node->save();
    }
    return TRUE;
    /*

    $sportTags = $getInfoObj->getTaxonomyByAPIID($event["sport"]["id"]);
    //Update General Event Meta
    if (isset($event['meta'])) {
      $ChannelbyNode = $getInfoObj->getIdChannelByNode($event['meta']);
      $node->field_game_channels_ref = $ChannelbyNode;

    }
    if (isset($event['streamprovider'])) {
      $stream = $creatorObj->createStreamPages($event['streamprovider'], $sportTags->id());
      $node->field_stream_provider_gp = $stream;
    }
    if (isset($event['streamprovider'])) {
      $Tags_Team = $creatorObj->createParticipantPages($event['participants'], $sportTags);
      $node->field_game_participants_tax = $Tags_Team;
    }*/
  }

  public function updateChanel($nodeId, $channel) {
    $node = Node::load($nodeId);
    $changed = FALSE;
    if (isset($node)) {

      if ($node->field_channel_description->value‎ != $channel['description'] and $channel['description'] != NULL) {
        $node->field_channel_description‎->value‎ = $channel['description'];
        $changed = TRUE;
      }
      if ($node->field_channel_notes->value‎ != $channel['notes'] and $channel['notes'] != NULL) {
        $node->field_channel_notes->value‎ = $channel['notes'];
        $changed = TRUE;
      }

      if ($changed == TRUE) {
        $node->save();
        echo 'Updating node (Channels) "' . $node->getTitle() . '- at ' . date("h:i:s") . "\n";
      }
    }
    return TRUE;
  }

  public function updateMetaByChanel() {
  }

  public function UpdateClienetData() {
  }

}
