<?php

namespace Drupal\rp_repo\Controller\oldVersion;

use Cocur\Slugify\Slugify;
use Drupal\Core\Controller\ControllerBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\rp_api\RPAPIClient;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveTimeZone;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;

/**
 * Class CreateClientData.
 */
class CreateClientData extends ControllerBase {

  //------------------------------------------------------------ NODE CREATION ----------------------------

  /**
   * function createStreamPages ()
   *
   * - $streams - Array of stream from Steve API
   *   streamprovider: [{
   * id: 1,
   * meta: [
   * { channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ]
   * }...]
   * - $sport_tags - Taxonomy ID to sport  (Example. 2145)
   */
  public function createStreamPages($streams, $sport_tags) {
    $tags_array = [];
    $getInfoObj = new RepoGeneralGetInfo();
    $rpClient = RPAPIClient::getClient();
    foreach ($streams as $stream) {
      $actualStream = $getInfoObj->getTaxonomyByCriterio($stream['id'], 'field_stream_provider_api_id');
      if (empty($actualStream)) {
        $streamObj = $rpClient->getStreamprovidersbyID(['id' => $stream['id']]);
        $streamType = $streamObj['type'];
        $requestType = [
          'field_stream_provider_type_apiid' => $streamType['id'],
          'name' => $streamType['name'],
        ];
        $taxonomyStreamType = $getInfoObj->getTaxonomyByCriterioMultiple($requestType);
        if (empty($taxonomyStreamType)) {
          $obj = [
            'name' => $streamType['name'],
            'vid' => 'stream_provider',
            //'field_streamprovider_logo'=>'',
            //'field_streamprovider_logo_date'=>'',
            'field_stream_provider_api_id' => '',
            'field_stream_provider_type_apiid' => $streamType['id'],
            'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
              'vid' => 'jsonld_',
              'name' => 'Streams',
            ]))->id(),
          ];
          $this->createGenericTaxonomy($obj);
          $parent = $getInfoObj->getTaxonomyByCriterioMultiple($requestType);
        }
        $requestStream = [
          'field_stream_provider_api_id' => $streamObj['id'],
          'name' => $streamObj['name'],
        ];
        $taxonomyStream = $getInfoObj->getTaxonomyByCriterioMultiple($requestStream);
        if (empty($taxonomyStream)) {
          $parent = $getInfoObj->getTaxonomyByCriterioMultiple($requestType);
          $data = $getInfoObj->getClearUrl(strtolower($streamObj['name']));
          //   $alias = $getInfoObj->getMultiplesAlias($data);
          $obj = [
            'name' => $streamObj['name'],
            'vid' => 'stream_provider',
            'parent' => $parent,
            'field_stream_provider_api_id' => $streamObj['id'],
            'field_stream_provider_type_apiid' => $streamType['id'],
            'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
              'vid' => 'jsonld_',
              'name' => 'Streams',
            ]))->id(),
            //  'path' => '/'.$data,
          ];
          $taxonomystream = $this->createGenericTaxonomy($obj, FALSE);
          $taxonomystreamId = $taxonomystream->id();
          $tags_array [] = ['target_id' => $taxonomystreamId];
        }
      }
      else {
        $tags_array [] = ['target_id' => $actualStream->id()];
      }
    }
    return $tags_array;
  }

  /**
   * function createGenericTaxonomy ( )
   * - $obj (array of fields an data)
   * $obj = [
   * 'parent' => [],
   * 'name' => $name,
   * 'vid' => $voc,
   * 'field_participant_api_id' => $idApi,
   * ]
   * return an object or an array if $reset is true
   */
  public function createGenericTaxonomy($obj, $reset = TRUE) {
    $term = Term::create($obj);
    $term->save();
    if ($reset) {
      $term = reset($term);
    }
    return $term;
  }

  /**
   * function createStreamPages ()
   * - $competition - Array  from Steve API
   *   competition: {
   * id: 153,
   * meta: [ {channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ]
   *              }
   *
   *
   * - $sport_tags - Taxonomy ID to sport  (Example. 2145)
   * - $sportName - Sport name (Example. Tennis )
   */
  public function createTournamentPages($competition, $sport_tags, $sportName, $langId) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($competition['id'], 'field_competition_id');
    if (empty($taxonomyCompetition)) {
      $rpClient = RPAPIClient::getClient();
      $parameters = [
        'id' => $competition['id'],
        'include_locales' => 1,
      ];
      $competition = $rpClient->getCompetitionsbyID($parameters);
      $taxonomy = new  taxonomySteveTimeZone();
      $sportName = $taxonomy->getLocaleName($competition["locales"], $langId);
      $competitionName = $sportName;
      $logo_path = $competition["logo"];
      $logo_modified = $competition["modified"];
      $locale = json_encode($competition["locales"]);
      $logo = $getInfoObj->getImg($logo_path, $getInfoObj->getClearUrl($competitionName . '_logo'));
      $competition_array[0] = $competition;
      $NewTax = $this->createTournametTaxonomy($competition_array, 'sport', $sport_tags, $sportName, $logo, $logo_modified, $locale);
      return $NewTax;
    }
    else {
      $node = $taxonomyCompetition->id();
      /* $updateObj = new UpdateClienetData();
       $updateObj->updateTournament($competition['id']);*/
    }
    return $node;

  }

  /**
   * function createTournametTaxonomy ( )
   * - $tournament (array from Steve API)
   * - $voc (vocavulary id)
   * - $sport_tags (name of item)
   * - $sport_id (sport taxonomy id)
   * - $logo (img id)
   * - $logo_modified (date of modified logo)
   * return taxonomy id
   */
  public function createTournametTaxonomy($tournament, $voc, $sport_tags, $sport_id, $logo, $logo_modified, $locale = []) {
    $getInfoObj = new RepoGeneralGetInfo();
    $tournamentId = $tournament[0]["id"];
    $tournamentName = $tournament[0]["name"];
    $tournamentParent = $tournament[0]["parent"];
    $tournamentSport = $tournament[0]["sport"];
    $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
    if (isset($taxonomy) or $taxonomy == FALSE) {
      if ($tournamentParent == NULL) {
        $data = $getInfoObj->getClearUrl(strtolower($tournamentName));
        $taxonomySport = $getInfoObj->getTaxonomyByAPIID('sport_' . 'sport_' . $tournamentSport);
        $obj = [
          'parent' => [$sport_tags],
          'name' => $tournamentName,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => '',
          // 'field_sport_api_id' => $sport_id,
          'field_competition_id' => $tournamentId,
          'field_content' => '',
          'field_locales' => $locale,
          'field_logo' => $logo,
          'field_logo_date' => $logo_modified,
          'field_weight' => rand(1, 1000),
          'field_sport_api_id' => $taxonomySport->id(),
          'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
            'vid' => 'jsonld_',
            'name' => 'Leages',
          ]))->id(),

        ];
        $tournamentTaxonomy = $this->createGenericTaxonomy($obj, FALSE);
        return $tournamentTaxonomy->id();
      }
      else {
        $competition = $getInfoObj->getTaxonomyParent($tournament);
        $index = count($competition) - 2;
        if ($index < 0) {
          $index = 0;
        }
        for ($i = $index; $i >= 0; $i--) {
          $id = $competition[$i]["id"];
          $name = $competition[$i]["name"];
          $tournamentId = $competition[$i]["id"];
          $parent_id = $competition[$i]["parent"];
          $parent_locale = json_encode($competition[$i]["locales"]);
          $taxonomy = $getInfoObj->getTaxonomyByAPIID($id);
          if (empty($taxonomy) or $taxonomy == FALSE) {
            if ($parent_id != NULL) {
              $parentTaxonomyId = $competition[$i + 1]["id"];
              $parentTaxonomyId = $getInfoObj->getTaxonomyByAPIID($parentTaxonomyId);
              $parentTaxonomyId = $parentTaxonomyId->id();
              $obj = [
                'parent' => [$parentTaxonomyId],
                'name' => $name,
                'vid' => $voc,
                'field_api_id' => $tournamentId,
                'field_api_parent' => $parentTaxonomyId,
                'field_sport_api_id' => $sport_id,
                'field_competition_id' => $tournamentId,
                'field_content' => '',
                'field_logo' => $logo,
                'field_locales' => $parent_locale,
                'field_logo_date' => $logo_modified,
                'field_weight' => rand(1, 1000),
                'field_sport_api_id' => $sport_tags,
                'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
                  'vid' => 'jsonld_',
                  'name' => 'Leages',
                ]))->id(),

              ];
              $this->createGenericTaxonomy($obj);
            }
            else {
              $data = $getInfoObj->getClearUrl(strtolower($name));
              $obj = [
                'parent' => [$sport_tags],
                'name' => $name,
                'vid' => $voc,
                'field_api_id' => $tournamentId,
                'field_api_parent' => 'Sport id: ' . $sport_id,
                'field_sport_api_id' => $sport_id,
                'field_competition_id' => $tournamentId,
                'field_content' => '',
                'field_logo' => $logo,
                'field_locales' => $parent_locale,
                'field_weight' => rand(1, 1000),
                'field_logo_date' => $logo_modified,
                'field_sport_api_id' => $sport_tags,
                'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
                  'vid' => 'jsonld_',
                  'name' => 'Sport',
                ]))->id(),
              ];
              $this->createGenericTaxonomy($obj);
            }
          }
        }
      }
    }
    $tournamentID = $competition[0]["id"];
    $tournamentTaxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentID);
    $id = $tournamentTaxonomy->id();
    print ' Creating Tournament Taxonomy - ' . $tournamentName . '  -  ' . $id . ' - at ' . date("h:i:s") . "\n";
    return $id;
  }


  /**
   * function createChannelsPages (Import all channels )
   *
   */
  public function createChannelsPages() {
    $rpClient = RPAPIClient::getClient();
    $AllChannel = $rpClient->getChannel();
    $getInfoObj = new RepoGeneralGetInfo();
    foreach ($AllChannel as $channel) {
      $oldChanel = $getInfoObj->getTaxonomyByCriterio($channel['id'], 'field_channel_api_id');
      if (empty($oldChanel)) {
        $newChanel = [
          'vid' => 'channels',
          'name' => $channel['name'],
          'field_channel_api_id' => $channel['id'],
          'field_channel_name' => $channel['name'],
          'field_channel_code' => $channel['name'],
          'field_channel_des' => $channel['description'],
          'field_channel_notes' => $channel['notes'],
        ];
        $this->createGenericTaxonomy($newChanel);
        print ' Creating node (Channels) "' . $channel['name'] . ' - at ' . date("h:i:s") . "\n";
      }
      /*  else {
          $nodeId = reset($oldChanel)->id();
          $updateObj = new UpdateClienetData();
          $updateObj->updateChanel($nodeId, $oldChanel);
        }*/
    }
    return TRUE;
  }

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
  public function createGamePage($sport_tags = '', $event, $stream = '', $defautltText, $Tags_Team, $ChannelByNode = '', $region, $tournamentID) {
    $getInfoObj = new RepoGeneralGetInfo();
    $sport = $getInfoObj->getTaxonomyByAPIID($sport_tags);
    $slugify = new Slugify();
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
        'field_events_sport' => $sport->id(),
        'field_events_properties' => $properties,
        'field_event_stream_provider' => $stream,
        'field_event_tournament' => $tournamentID,
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
          'vid' => 'jsonld_',
          'name' => 'Events',
        ]))->id(),
        'title' => $event['name'],
      ];
      $node = $this->createNodeGeneric($node);
      $alias = '/' . $slugify->slugify($sport->name->value) . '/' . $slugify->slugify($getInfoObj->getClearUrl($event['name']));
      // \Drupal::service('path.alias_storage')->save("/node/" . $node, $alias, $region);
    }
    print ' Creating Game Page - ' . $event['name'] . ' - at ' . date("h:i:s") . "\n";
    print "\n";
    return $node;
  }

  /**
   * function createParticipantPages ( )
   * - $partArray ( Participants array from Steve - API )
   *    participants: [{
   * id: 687,
   * meta: [
   * {channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ],
   * name: "Trelleborgs FF",
   * running_order: 2
   * },{
   * id: 864,
   * meta: [
   * {channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ],
   * name: "Örgryte IS",
   * running_order: 1
   * }
   * ]
   * -  $sport_tags ( Sport Taxonomy id )
   */
  public function createParticipantPages($partArray, $sport_tags, $langId) {

    $tags_team_array = [];
    $getInfoObj = new RepoGeneralGetInfo();
    foreach ($partArray as $team) {
      $id = $team['id'];
      $opc = 'field_participant_api_id';
      $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($id, $opc);
      if (empty($taxonomyCompetition)) {

        $parameters = ['id' => $team ['id'], 'include_locales' => 1];
        $rpClient = RPAPIClient::getClient();
        $Participants = $rpClient->getParticipantsbyID($parameters);
        $logo_path = $Participants["logo"];
        $logo_modified = $Participants["modified"];
        $locales = json_encode($Participants["locales"]);
        $taxonomy = new  taxonomySteveTimeZone();
        $name = $taxonomy->getLocaleName($Participants["locales"], $langId);
        $logo = $getInfoObj->getImg($logo_path, $getInfoObj->getClearUrl($name . '_logo'), 'team');
        $obj = [
          'parent' => [],
          'name' => $name,
          'vid' => 'participant',
          'field_participant_api_id' => $team ['id'],
          'field_participant_content' => '',
          'field_participant_logo' => $logo,
          'field_participant_logo_date' => $logo_modified,
          'field_participant_sport' => $sport_tags,
          'field_locales' => $locales,
          'field_weight' => rand(1, 1000),
          'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
            'vid' => 'jsonld_',
            'name' => 'Participants',
          ]))->id(),
        ];
        $this->createGenericTaxonomy($obj);
        $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($id, $opc);
        $team_tax = $taxonomyCompetition->id();
        print ' Creating Participant - ' . $name . '- at ' . date("h:i:s") . "\n";
      }
      else {
        $team_tax = $taxonomyCompetition->id();

      }
      $tags_team_array [] = ['target_id' => $team_tax];
    }
    return $tags_team_array;
  }

  /**
   * function createMeta ( )
   * - $event (Event Array from Steve API)
   */
  public function createMeta($event) {

    //General Variable
    $eventID = $event['id'];
    $eventName = $event['id'] . '/' . $event['name'];
    $eventName = substr($eventName, 0, 25);
    // Sport Variable
    $metaSportArray = $event['sport'];
    $metaCompetitionArray = $event['competition'];
    $metaEventArray = $event['meta'];
    $metaParticipantsArray = $event['participants'];
    $metaStreamproviderArray = $event['streamprovider'];
    $data = [
      "sport" => $metaSportArray,
      "competition" => $metaCompetitionArray,
      "event" => $metaEventArray,
      "participants" => $metaParticipantsArray,
      "streamers" => $metaStreamproviderArray,
    ];
    $this->createSimpleMeta($eventName, $eventID, $data);
    print "\n Insert Metas";
    //---------------------------------------------------------------------------------------------------------------------------------//
    return TRUE;
  }

  //---------------------------------------------------------- ENTITY "META" CREATION ----------------------------

  /**
   * function createParticipantTaxonomy ( )
   * - $name (name of item)
   * - $voc (vocavulary id)
   * - $idApi (Participant API id)
   * return taxonomy id
   */
  public function createParticipantTaxonomy($name, $voc, $idApi, $locale) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomy = $getInfoObj->getTaxonomyByParticipantAPIID($idApi);

    if (!$taxonomy or empty($taxonomy)) {
      $term = Term::create([
        'parent' => [],
        'name' => $name,
        'vid' => $voc,
        'field_locales' => $locale,
        'field_participant_api_id' => $idApi,
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
          'vid' => 'jsonld_',
          'name' => 'Participants',
        ]))->id(),
      ]);
      $term->save();
      $taxonomy = $getInfoObj->getTaxonomyByParticipantAPIID($idApi);
    }
    print ' Creating Participant Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
    return $taxonomy->id();

  }

  /**
   * function createParticipantPages ( )
   * - $partArray ( Participants array from Steve - API )
   *    participants: [{
   * id: 687,
   * meta: [
   * {channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ],
   * name: "Trelleborgs FF",
   * running_order: 2
   * },{
   * id: 864,
   * meta: [
   * {channel: 1},
   * {channel: 2},
   * {channel: 3},
   * {channel: 4}
   * ],
   * name: "Örgryte IS",
   * running_order: 1
   * }
   * ]
   * -  $sport_tags ( Sport Taxonomy id )
   *
   * public function createParticipantPages($partArray, $sport_tags)
   * {
   * $tags_team_array = [];
   * $getInfoObj = new RepoGeneralGetInfo();
   * foreach ($partArray as $team) {
   * $id = $team['id'];
   * $name = $team['name'];
   * $opc = 'field_participant_api_id';
   * $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($id, $opc);
   * if (empty($taxonomyCompetition)) {
   * $parameters = ['id' => $team ['id'], 'include_locales' => 1];
   * $rpClient = RPAPIClient::getClient();
   * $Participants = $rpClient->getParticipantsbyID($parameters);
   * $logo_path = $Participants["logo"];
   * $logo_modified = $Participants["modified"];
   * $locales = json_encode($Participants["locales"]);
   * $logo = $getInfoObj->getImg($logo_path, $getInfoObj->getClearUrl($name .
   * '_logo'), 'team');
   * $data = $getInfoObj->getClearUrl(strtolower($name));
   * $alias = $getInfoObj->getMultiplesAlias($data);
   * $obj = [
   * 'parent' => [],
   * 'name' => $name,
   * 'vid' => 'participant',
   * 'field_participant_api_id' => $team ['id'],
   * 'field_participant_content' => '',
   * 'field_participant_logo' => $logo,
   * 'field_participant_logo_date' => $logo_modified,
   * 'field_participant_sport' => $sport_tags,
   * 'field_locales' => $locales,
   * 'field_weight' => rand(1, 500),
   * 'field_jsonld_struct' =>
   * ($getInfoObj->getTaxonomyByCriterioMultiple(array('vid' => 'jsonld_',
   * 'name' => 'Participants')))->id(),
   * ];
   * $this->createGenericTaxonomy($obj);
   * $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($id, $opc);
   * $team_tax = $taxonomyCompetition->id();
   * print ' Creating Participant - ' . $team ['name'] . '- at ' .
   * date("h:i:s") . "\n";
   * } else {
   * $team_tax = $taxonomyCompetition->id();
   *
   * }
   * $tags_team_array [] = ['target_id' => $team_tax];
   *
   *
   * return $tags_team_array;
   * }
   * }
   *
   *
   * /**
   * function createChannelsPages ( )
   *
   *
   * public function createSimpleMeta($eventName, $eventID, $data) {
   *
   * $data = [
   * 'name' => $eventName,
   * 'field_events_apiid' => $eventID,
   * 'field_properties' => json_encode($data),
   * ];
   * $node = \Drupal::entityTypeManager()
   * ->getStorage('channels_by_contenttype')
   * ->create($data);
   * $node->save();
   * }
   * /**
   * function createChannelsPages ( )
   *
   *
   * public function createMultipleMeta($eventID, $metaDescriptionArray,
   * $metaName, $nodeGPID, $token) { foreach ($metaDescriptionArray as
   * $metaArray) { foreach ($metaArray["meta"] as $array) {
   * $data = [
   * 'name' => $metaName . $metaArray['id'] . ' - channel/' .
   * $array['channel'],
   * 'field_cannel_apiid' => $array['channel'],
   * 'field_order_priority' => $metaArray['running_order'],
   * 'field_events_apiid' => $eventID,
   * 'field_meta_type_event_sport_part' => $metaName,
   * 'field_meta_type_api_id_event_id' => $metaArray['id'],
   * 'field_properties' => json_encode($array['properties']),
   * 'field_game_page_taxonomy' => $nodeGPID,
   * 'field_token' => $token,
   * ];
   * $node = \Drupal::entityTypeManager()
   * ->getStorage('channels_by_contenttype')
   * ->create($data);
   * $node->save();
   * }
   * }
   * }
   */

  //-------------------------------------------------------- TAXONOMY CREATION ----------------------------

  /**
   * function createVocabulary
   *
   */
  public function createVocabulary() {
    $vocabularys = ['sport', 'stream_provider', 'participant', 'tournament'];
    foreach ($vocabularys as $vocal) {
      $vocabulary = \Drupal\taxonomy\Entity\Vocabulary::create([
        'vid' => $vocal,
        'description' => '',
        'name' => $vocal,
      ]);
      print ' Creating Vocabulary -' . $vocal . ' - at ' . date("h:i:s") . "\n";
      $vocabulary->save();
    }
    return TRUE;
  }

  /**
   * function createChannelsPages ( )
   *
   */
  public function createSportPages_multiplesMenu($sport_id) {
    $rpClient = RPAPIClient::getClient();
    $para = ['id' => $sport_id];
    $getInfoObj = new RepoGeneralGetInfo();
    $node_id = $getInfoObj->getNode($sport_id, 'sport_pages', 'field_sport_api_id');
    if (empty($node_id)) {
      $competition = $rpClient->getSportbyID($para);
      $name = $competition["name"];
      $id_api = $competition["id"];
      $slugify = new Slugify();
      $node = [
        'type' => 'sport_pages',
        'title' => $name,
        'field_sport_name' => $name,
        'field_sport_api_id' => $id_api,
        'field_sport_theme_descrption' => $getInfoObj->getDefaultSportPage($name),
        // 'path' => ['alias' => '/' . $slugify->slugify($name)],
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
          'vid' => 'jsonld_',
          'name' => 'Site',
        ]))->id(),
        'body' => [
          'value' => $getInfoObj->getDefaultSportPage($name),
          'summary' => 'Live stream ' . $name . ' | Online ' . $name . ' i dag ',
          'format ' => 'full_html',
        ],
      ];
      $new_node = $this->createNodeGeneric($node);

      $menu_name = [
        'TopSportMenu',
        'FrontTopSportMenu',
        'FrontButtomSportMenu',
      ];
      $description = [
        'General Top Sport Menu',
        'Front Page Top Sport Menu',
        'Front Page Buttom Sport Menu',
      ];

      $term = $this->createTaxonomy($name, 'sport');
      $sport_tags = $term->id();
      $this->createMenu($menu_name, $description, $new_node, $name);
      $this->createSportPages($sport_id);
      print ' Creating Sport Pages -' . $name . '- at ' . date("h:i:s") . "\n";
    }
    else {
      $competition = $rpClient->getSportbyID($para);
      $name_sport = $competition["name"];
      $term = $getInfoObj->getTaxonomy($name_sport);
      $sport_tags = reset($term)->id();
      print 'Get Sport Pages Taxonomy-' . $name_sport . '- at ' . date("h:i:s") . "\n";
    }
    return $sport_tags;
  }

  /**
   * function createTaxonomy ( )
   * - $name (name of item)
   * - $voc (vocavulary id)
   * return an array
   */
  public function createTaxonomy($name, $voc) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomy = $getInfoObj->getTaxonomy($name);
    if (!$taxonomy) {
      $term = Term::create([
        'parent' => [],
        'name' => $name,
        'vid' => $voc,
      ]);
      $term->save();
      $taxonomy = $getInfoObj->getTaxonomy($name);
    }
    // print_r( ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n");
    $term = reset($taxonomy);
    return $term;

  }

  /**
   * function createChannelsPages ( )
   *
   */
  public function createMenu($menu_name, $description, $node, $sport) {
    for ($i = 0; $i < count($menu_name); $i++) {
      $menuid = $menu_name[$i] . '_id';
      $id_node = \Drupal::entityTypeManager()
        ->getStorage('menu')
        ->loadByProperties(['id' => $menuid]);
      if (!$id_node) {
        $menu = \Drupal::entityTypeManager()->getStorage('menu')
          ->create([
            'id' => $menuid,
            'label' => $menu_name[$i],
            'menu_name' => $menu_name[$i],
            'description' => $description[$i],
          ])->save();
        print " Creating Menu $menu_name[$i]" . ' - at ' . date("h:i:s") . "\n";
      }

      $menu_link = \Drupal::entityTypeManager()
        ->getStorage('menu_link_content')
        ->loadByProperties(['menu_name' => $menuid, 'title' => $sport]);

      if (empty($menu_link)) {
        $menu_link = MenuLinkContent::create([
          'title' => $sport,
          'link' => ['uri' => 'internal:/node/' . $node],
          'menu_name' => $menuid,
          'expanded' => TRUE,
        ])->save();
        print " Creating menu item $sport on Menu $menu_name[$i]" . ' - at ' . date("h:i:s") . "\n";
      }
    }
    return TRUE;
  }

  /**
   * Funtion createSportPages ( create a Taxonomy,sport pagas to a specify
   * sport )
   * - $sportDrupalId (drupal specify id for sports - Example. sport_1 )
   * - $sportApiId ( Steve API Sport id -  Example. 1 )
   * - $region ( Steve API region - Example. en_EG)
   */
  public function createSportPages($array) {
    $sportName = $array["sportName"];
    $taxonomySportId = $array["taxonomySportId"];
    $region = $array["region"];
    $json = $array["json"];
    $sportDrupalId= $array["sportDrupalId"];
      $node = [
        'type' => 'sport',
        'title' => $sportName,
        'field_sport_sport' => $taxonomySportId,
        'field_sport_theme_properties' => '',
        'langcode' => $region,
        'field_jsonld_struct' =>  $json,
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
      ];
      print 'Creating Sport Pages -' . $sportName . ' - at ' . date("h:i:s") . "\n";
      $newNode = $this->createNodeGeneric($node);
      $this->createItemMenu('main', '', $newNode, $sportName, $taxonomySportId, $sportDrupalId, $region);

   // }
    /*else {
            $updateOBJ = new UpdateClienetData();
            $competitionName = $TaxonomyObj->name->value;
            $taxonomySportId = $TaxonomyObj->id();
            $sportNode = reset($getInfoObj->getNode($taxonomySportId, 'sport', 'field_sport_sport'));
            $updateOBJ->createTranslation($sportNode, $region, [], '/' . $region . '/' . $sportNode->getTitle());

            $sportNode = reset($getInfoObj->getNode($taxonomySportId, 'sport_blogs', 'field_sport_blogs_sport'));
            $updateOBJ->createTranslation($sportNode, $region, [], '/' . $region . '/' . $sportNode->getTitle());

            $sportNode = reset($getInfoObj->getNode($taxonomySportId, 'sport_stream_reviews', 'field_sport_stream_reviews_sport'));
            $updateOBJ->createTranslation($sportNode, $region, [], '/' . $region . '/' . $sportNode->getTitle());


            print "\n";
            print ' Using Sport - ' . $competitionName . ' ( ' . $region . ' ) - at ' . date("h:i:s") . "\n";
        }*/
    return $taxonomySportId;
  }

  /**
   * function createChannelsPages ( )
   *
   */
  public function defaultTournamentTaxonomy($parentId, $name, $voc, $tournamentId, $tournamentParents, $sport_id, $jsonld = 'Leages', $color, $locales = []) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomy = $getInfoObj->getTaxonomyByCriterioMultiple(['vid' => 'sport','field_api_id'=>$tournamentId]);
    if ($taxonomy == FALSE) {
      if ($voc = 'sport') {
        $term = Term::create([
          'parent' => [$parentId],
          'name' => $name,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => $tournamentParents,
          'field_sport_api_id' => $sport_id,
          'field_base_color' => $color,
          'field_locales' => $locales,
          'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
            'vid' => 'jsonld_',
            'name' => $jsonld,
          ]))->id(),
        ]);
      }
      else {
        $term = Term::create([
          'parent' => [$parentId],
          'name' => $name,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => $tournamentParents,
          'field_sport_api_id' => $sport_id,
          'field_base_color' => $color,
          'field_base_color' => $color,
          'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
            'vid' => 'jsonld_',
            'name' => $jsonld,
          ]))->id(),
        ]);
      }

      $term->save();
      print ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
      print ' Creating Tournament Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
      $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
    }
    return $taxonomy->id();

  }


  public function createJsonLdTaxonomy($voc = 'jsonld_', $data = [
    'Events',
    'Leages',
    'Participants',
    'Personal',
    'Place',
    'Site',
    'Streams',
    'Sport',
    'Reviews',
    'Blog',
  ]) {
    foreach ($data as $tName) {
      $this->createTaxonomy($tName, $voc);
    }
    return TRUE;
  }

  //-------------------------------------------------------- Generic CREATION ----------------------------

  /**
   * function createChannelsPages ( )
   *
   */
  public function createItemMenu($menu_name, $description, $nodeId, $sport, $sport_tags, $sport_id, $region) {

    /*---------- new menu----------- */
    $id_node = \Drupal::entityTypeManager()->getStorage('menu')->loadByProperties(['id' => $menu_name]);
    if (!$id_node) {
      $menu = \Drupal::entityTypeManager()->getStorage('menu')->create([
        'id' => $menu_name,
        'label' => $menu_name,
        'menu_name' => $menu_name,
        'description' => $description,
      ])->save();
      print " Creating Menu $menu_name" . ' - at ' . date("h:i:s") . "\n";
    }
   /*--------------------- */

    $menu_link = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties(['menu_name' => $menu_name, 'title' => $sport]);
    if (empty($menu_link)) {
       MenuLinkContent::create([
        'title' => $sport,
        'link' => ['uri' => 'internal:/node/' . $nodeId],
        'description' => $sport_tags,
        'menu_name' => $menu_name,
        'parent' => 'null',
        'expanded' => TRUE,
      ])->save();

      $menu_link_SportObj = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties(['description' => $sport_tags]);
      $uuid = reset($menu_link_SportObj)->uuid->value;

      if ($uuid) {
        $menuID = 'main';
        $forside = $sport . ' Forside';
        $this->createGenericItemMenu($menuID, $forside, $nodeId, $uuid, -99);

        $LiveStream = 'Live Stream ' . $sport;
        $url = 'Live Stream ' . $sport;
        $id = 'liveStream';
        $LiveStream_id = $this->createSportInternPages($sport_tags, $sport, $LiveStream, $id, $url, $sport_id, 'sport_stream_reviews', $region);
        $this->createGenericItemMenu($menuID, $LiveStream, $LiveStream_id, $uuid, -98);

        $Blog = 'Blog ' . $sport;
        $url = 'Blog';
        $id = 'blog';
        $Blog_id = $this->createSportInternBlog($sport_tags, $sport, $Blog, $id, $url, $sport_id, 'sport_blogs', $region);
        $this->createGenericItemMenu($menuID, $Blog, $Blog_id, $uuid, -97);
      }
      print " Creating menuitem $sport on Menu $menu_name" . ' - at ' . date("h:i:s") . "\n";
    }
    return TRUE;
  }

  /**
   * function createChannelsPages ( )
   *
   */
  public function createGenericItemMenu($menuID, $title, $nodeID, $parent, $weight) {
    MenuLinkContent::create([
      'title' => $title,
      'link' => ['uri' => 'internal:/node/' . $nodeID],
      'menu_name' => $menuID,
      'expanded' => TRUE,
      'parent' => 'menu_link_content:' . $parent,
      'weight' => $weight,
    ])->save();
    print " Creating menu item '. $title . ' on Menu" . ' - at ' . date("h:i:s") . "\n";
    return TRUE;
  }

  /**
   * function createSportInternPages ()
   * - $sportTags - Taxonomy ID to sport  (Example. 2145)
   * - $sportName - Sport name (Example. Tennis )
   * - $PageTitle - Page Title (Example. Live Stream Tennis)
   * - $id - Page id (Example. Live Stream)
   * - $url - Page last part alias url (Example. Tennis )
   * - $sportApiId - sport id  (Example. 5)
   * - $type - content type. (Example.sport_blogs)
   * - $region -  ( Steve API region - Example. en_EG)
   */

  public function createSportInternPages($sportTags, $sportName, $PageTitle, $id, $url, $sportApiId, $type, $region) {
    if ($type) {
      $getInfoObj = new RepoGeneralGetInfo();
      $Aliasdata = $region . '/' . $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url);
      //  $alias = $getInfoObj->getMultiplesAlias($Aliasdata);
      $createStreamPages = [
        'type' => $type,
        'title' => $PageTitle,
        'field_sport_stream_reviews_sport' => $sportTags,
        'field_sport_review_properties' => '',
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
          'vid' => 'jsonld_',
          'name' => 'Reviews',
        ]))->id(),
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
        //'path' => '/'.$getInfoObj->getClearUrl($PageTitle),
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      print ' Creating Sport Intern Pages - ' . $PageTitle . ' - at ' . date("h:i:s") . "\n";
    }
    return $id;

  }

  /**
   * function createNodeGeneric
   *
   */

  public function createNodeGeneric($data) {
    $node = Node::create($data);
    $node->save();
    return $node->id();
  }

  /**
   * function createSportInternPages ()
   * - $sportTags - Taxonomy ID to sport  (Example. 2145)
   * - $sportName - Sport name (Example. Tennis )
   * - $PageTitle - Page Title (Example. Live Stream Tennis)
   * - $id - Page id (Example. Live Stream)
   * - $url - Page last part alias url (Example. Tennis )
   * - $sportApiId - sport id  (Example. 5)
   * - $type - content type. (Example.sport_blogs)
   * - $region -  ( Steve API region - Example. en_EG)
   */

  public function createSportInternBlog($sportTags, $sportName, $name, $id, $url, $sportApiId, $type, $region) {
    $getInfoObj = new RepoGeneralGetInfo();
    if ($type) {
      $getInfoObj = new RepoGeneralGetInfo();
      $Aliasdata = $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url);
      // $alias = $getInfoObj->getMultiplesAlias($Aliasdata);
      $createStreamPages = [
        'type' => $type,
        'title' => $name,
        'field_sport_blogs_sport' => $sportTags,
        'field_sport_theme_blog_propertie' => '',
        'field_jsonld_struct' => ($getInfoObj->getTaxonomyByCriterioMultiple([
          'vid' => 'jsonld_',
          'name' => 'Blog',
        ]))->id(),
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
        // 'path' => '/'.$getInfoObj->getClearUrl($name),
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      print ' Creating Sport Internal Blog Page - ' . $name . '- at ' . date("h:i:s") . "\n";
    }
    return $id;

  }

  /**
   * function createChannelsPages ( )
   *
   */
  public function CreateClientData() {
    return TRUE;
  }

}
