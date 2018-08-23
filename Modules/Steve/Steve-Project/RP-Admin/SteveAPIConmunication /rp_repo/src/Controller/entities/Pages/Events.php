<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 4:04 PM
 */

namespace Drupal\rp_repo\Controller\entities\Pages;

use Drupal\rp_repo\Controller\entities\Pages\pages;

class Events extends pages
{
  /**
   * function createGamePage ( Create Events page on events content type)
   * - $sport_tags ( Sport Taxonomy id )
   * - $event (Array from Steve API)
   * - $stream (Stream Array Taxonomies ids  )
   * - $defautltText (text by default)
   * - $Tags_Team (Teams Array Taxonomies ids )
   * - $ChannelByNode (Channel Array Taxonomies ids )
   * - $region  ( Steve API region - Example. en_EG)
   */
  public function createGamePage($sport_tags = '', $event, $stream = '', $defautltText, $Tags_Team, $ChannelByNode = '', $region)
  {
    $getInfoObj = new RepoGeneralGetInfo();
    $tournament = $getInfoObj->getTaxonomyByAPIID($event['competition']['id']);
    $sport = $getInfoObj->getTaxonomyByID($sport_tags);
    if (isset($event)) {
      $metaSportArray = $event['sport'];
      $metaCompetitionArray = $event['competition'];
      $metaEventArray = $event['meta'];
      $metaParticipantsArray = $event['participants'];
      $metaStreamproviderArray = $event['streamprovider'];
      $data[$region] = [
        "sport" => $metaSportArray,
        "competition" => $metaCompetitionArray,
        "event" => $metaEventArray,
        "participants" => $metaParticipantsArray,
        "streamers" => $metaStreamproviderArray,
      ];
      $properties = json_encode($data);

      $node = [
        'type' => 'events',
        'body' => [
          'value' => $defautltText,
          'summary' => $defautltText,
          'format ' => 'full_html',
        ],
        'field_event_channels' => $ChannelByNode,
        'field_event_date' => strtotime($event['start']),
        'field_event_api_id' => $event['id'],
        'field_event_participants' => $Tags_Team,
        'field_events_sport' => $sport_tags,
        'field_events_properties' => $properties,
        'field_event_stream_provider' => $stream,
        'field_event_tournament' => $tournament->id(),
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_', 'name' => 'Events')))->id(),
        'title' => $event['name']
      ];
      $node = $this->createNodeGeneric($node);
    }
    print ' Creating Game Page - ' . $event['name'] . ' - at ' . date("h:i:s") . "\n";
    print "\n";
    return $node;
  }
  public function updateEvents($event, $node_id, $region)
  {
    $node = Node::load($node_id);
    $data = json_decode($node->field_events_properties->value);
    $OldAlias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $node_id);
    $alias = '/' . $region . '' . $OldAlias;
    if (!isset($data->$region)) {
      $metaSportArray = $event['sport'];
      $metaCompetitionArray = $event['competition'];
      $metaEventArray = $event['meta'];
      $metaParticipantsArray = $event['participants'];
      $metaStreamproviderArray = $event['streamprovider'];
      $data->$region = [
        "sport" => $metaSportArray,
        "competition" => $metaCompetitionArray,
        "event" => $metaEventArray,
        "participants" => $metaParticipantsArray,
        "streamers" => $metaStreamproviderArray,
      ];
      $properties = json_encode($data);
      $this->createTranslation($node, $region, $properties, $alias);
    }
    return TRUE;
  }


}
