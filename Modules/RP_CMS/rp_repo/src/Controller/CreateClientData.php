<?php

namespace Drupal\rp_repo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\menu_link_content\Entity\MenuLinkContent;


use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\RepoGeneralGetInfo;
use Drupal\rp_repo\Controller\UpdateClienetData;

/**
 * Class CreateClientData.
 */
class CreateClientData extends ControllerBase {

  //------------------------------------------------------------ NODE CREATION ----------------------------

  public function createSportPages($sportDrupalId, $sportApiId,$region) {
    $rpClient = RPAPIClient::getClient();
    $getInfoObj = new RepoGeneralGetInfo();
    $parameters = ['id' => $sportApiId];
    $obj = ['field_api_id' => $sportDrupalId];
    $nodeObj = $getInfoObj->getTaxonomyByCriterioMultiple($obj);
    if (empty($nodeObj)) {
      $sport = $rpClient->getSportbyID($parameters);
      $sportName = $sport["name"];
      $taxonomySportId = $this->defaultTournamentTaxonomy('', $sportName, 'sport', $sportDrupalId, 'null', $sportDrupalId);
      $data = $getInfoObj->getClearUrl(strtolower($sportName));
      $alias =  $getInfoObj->getMultiplesAlias($data);
      $node = [
        'type' => 'sport',
        'title' => $sportName,
        'field_sport_sport' => $taxonomySportId,
        'field_sport_theme_properties' => '',
        'path' => $alias,
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
      ];
      $newNode = $this->createNodeGeneric($node);
      $this->createItemMenu('main', '', $newNode, $sportName, $taxonomySportId, $sportDrupalId,$region);
      echo 'Creating Sport Pages -' . $sportName . ' - at ' . date("h:i:s") . "\n";
    }
    else {
      $competitionName = $nodeObj->name->value;
      $taxonomySportId = $nodeObj->id();
      echo "\n";
      echo ' Using Sport - ' . $competitionName . ' ( ' .$region.' ) - at ' . date("h:i:s") . "\n";
    }
    return $taxonomySportId;
  }

  public function createSportInternPages($sportTags, $sportName, $name, $id, $url, $sportApiId, $type,$region) {
    if ($type) {
      $getInfoObj = new RepoGeneralGetInfo();
      $Aliasdata = $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url);
      $alias =  $getInfoObj->getMultiplesAlias($Aliasdata);

      $createStreamPages = [
        'type' => $type,
        'title' => $name,
        'field_sport_stream_reviews_sport' => $sportTags,
        'field_sport_review_properties' => '',
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
        'path' => $alias,
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      echo ' Creating Sport Intern Pages - ' . $name . ' - at ' . date("h:i:s") . "\n";
    }
    return $id;

  }

  public function createSportInternBlog($sportTags, $sportName, $name, $id, $url, $sportApiId, $type,$region) {
    $getInfoObj = new RepoGeneralGetInfo();
    if ($type) {
      $getInfoObj = new RepoGeneralGetInfo();
      $Aliasdata = $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url);
      $alias =  $getInfoObj->getMultiplesAlias($Aliasdata);
      $createStreamPages = [
        'type' => $type,
        'title' => $name,
        'field_sport_blogs_sport' => $sportTags,
        'field_sport_theme_blog_propertie' => '',
        'body' => [
          'value' => '',
          'summary' => '',
          'format ' => 'full_html',
        ],
        'path' => $alias, //['alias' => '/' .$region.'/' . $getInfoObj->getClearUrl($sportName) . '/' . $getInfoObj->getClearUrl($url)],
      ];
      $id = $this->createNodeGeneric($createStreamPages);
      echo ' Creating Sport Internal Blog Page - ' . $name . '- at ' . date("h:i:s") . "\n";
    }
    return $id;

  }

  public function createStreamPages($streams, $sport_tags) {
    $tags_array = array();
    $getInfoObj = new RepoGeneralGetInfo();
    $rpClient = RPAPIClient::getClient();
    foreach ($streams as $stream) {
      $actualStream = $getInfoObj->getTaxonomyByCriterio($stream['id'], 'field_stream_provider_api_id');
      if(empty($actualStream)){
          $streamObj = $rpClient->getStreamprovidersbyID(['id' => $stream['id']]);
          $streamType = $streamObj['type'];
          $requestType = array('field_stream_provider_type_apiid' => $streamType['id'],'name' => $streamType['name']);
          $taxonomyStreamType = $getInfoObj->getTaxonomyByCriterioMultiple($requestType);
          if (empty($taxonomyStreamType)) {
            $obj = [
              'name' => $streamType['name'],
              'vid' => 'stream_provider',
              //'field_streamprovider_logo'=>'',
              //'field_streamprovider_logo_date'=>'',
              'field_stream_provider_api_id' => '',
              'field_stream_provider_type_apiid' => $streamType['id'],
            ];
            $this->createGenericTaxonomy($obj);
            $parent = $getInfoObj->getTaxonomyByCriterioMultiple($requestType);
          }
          $requestStream = array(
            'field_stream_provider_api_id' => $streamObj['id'],
            'name' => $streamObj['name'],
          );
          $taxonomyStream = $getInfoObj->getTaxonomyByCriterioMultiple($requestStream);
          if (empty($taxonomyStream)) {
            $parent = $getInfoObj->getTaxonomyByCriterioMultiple($requestType);
            $data = $getInfoObj->getClearUrl(strtolower($streamObj['name']));
            $alias =  $getInfoObj->getMultiplesAlias($data);
            $obj = [
              'name' => $streamObj['name'],
              'vid' => 'stream_provider',
              'parent' => $parent,
              'field_stream_provider_api_id' => $streamObj['id'],
              'field_stream_provider_type_apiid' => $streamType['id'],
              'path' => $alias,
            ];
            $this->createGenericTaxonomy($obj);
            $taxonomystreamId = ($getInfoObj->getTaxonomyByCriterioMultiple($requestStream,0))->id();
            $tags_array [] = ['target_id' => $taxonomystreamId];
          }
        }
      else{
           $tags_array [] = ['target_id' => $actualStream->id()];
        }
    }
    return $tags_array;
  }

  public function createTournamentPages($competition, $sport_tags, $sportName) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($competition['id'], 'field_competition_id');
    if (empty($taxonomyCompetition)) {
      $rpClient = RPAPIClient::getClient();
      $parameters = ['id' => $competition['id']];
      $competition = $rpClient->getCompetitionsbyID($parameters);
      $competitionName = $competition[0]["name"];
      $logo_path = $competition[0]["logo_path"];
      $logo_modified = $competition[0]["logo_modified"];
      $logo = $getInfoObj->getImg($logo_path, $getInfoObj->getClearUrl($competitionName . '_logo'));
      $competition_array[0] = $competition;
      $NewTax = $this->createTournametTaxonomy($competition_array, 'sport', $sport_tags, $sportName, $logo, $logo_modified);
      return $NewTax;
    }
    else {
      $node = $taxonomyCompetition->id();
      /* $updateObj = new UpdateClienetData();
       $updateObj->updateTournament($competition['id']);*/
    }
    return $node;

  }

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
        echo ' Creating node (Channels) "' . $channel['name'] . ' - at ' . date("h:i:s") . "\n";
      }
      /*  else {
          $nodeId = reset($oldChanel)->id();
          $updateObj = new UpdateClienetData();
          $updateObj->updateChanel($nodeId, $oldChanel);
        }*/
    }
    return TRUE;
  }

  public function createGamePage($sport_tags = '', $event, $stream = '', $defautltText, $Tags_Team, $ChannelByNode = '',$region) {
    $getInfoObj = new RepoGeneralGetInfo();
    $tournament = $getInfoObj->getTaxonomyByAPIID($event['competition']['id']);
    $sport = $getInfoObj->getTaxonomyByID($sport_tags);
    if (isset($event)) {
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
      $properties = json_encode($data);
    }
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
      'field_events_properties'=> $properties,
      'field_event_stream_provider' => $stream,
      'field_event_tournament' => $tournament->id(),
      'title' => $event['name'],
      'path' => ['alias' => '/' . $getInfoObj->getClearUrl(reset($sport)["name"]["x-default"]) . '/' . $region. '/' . $getInfoObj->getClearUrl($event['name'])],
    ];
    $node = $this->createNodeGeneric($node);
    echo ' Creating Game Page - ' . $event['name'] . ' - at ' . date("h:i:s") . "\n";
    echo "\n";
    return $node;
  }

  public function createParticipantPages($partArray, $sport_tags) {
    $tags_team_array = [];
    $getInfoObj = new RepoGeneralGetInfo();
    foreach ($partArray as $team) {
      $id = $team['id'];
      $name = $team['name'];
      $opc = 'field_participant_api_id';
      $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($id, $opc);
      if (empty($taxonomyCompetition)) {
        $parameters = ['id' => $team ['id']];
        $rpClient = RPAPIClient::getClient();
        $Participants = $rpClient->getParticipantsbyID($parameters);
        $logo_path = $Participants[0]["logo_path"];
        $logo_modified = $Participants[0]["logo_modified"];
        $logo = $getInfoObj->getImg($logo_path, $getInfoObj->getClearUrl($name . '_logo'));

        $data = $getInfoObj->getClearUrl(strtolower($name));
        $alias =  $getInfoObj->getMultiplesAlias($data);

        $obj = [
          'parent' => [],
          'name' => $name,
          'vid' => 'participant',
          'field_participant_api_id' => $team ['id'],
          'field_participant_content' => '',
          'field_participant_logo' => $logo,
          'field_participant_logo_date' => $logo_modified,
          'field_participant_sport' => $sport_tags,
          'path' => $alias,
        ];
        $this->createGenericTaxonomy($obj);
        $taxonomyCompetition = $getInfoObj->getTaxonomyByCriterio($id, $opc);
        $team_tax = $taxonomyCompetition->id();
        echo ' Creating Participant - ' . $team ['name'] . '- at ' . date("h:i:s") . "\n";
      }
      else {
        $team_tax = $taxonomyCompetition->id();

      }
      $tags_team_array [] = ['target_id' => $team_tax];
    }
    return $tags_team_array;
  }

  //---------------------------------------------------------- ENTITY "META" CREATION ----------------------------

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
    echo "\n Insert Metas";


    //---------------------------------------------------------------------------------------------------------------------------------//
    return TRUE;
  }

  public function createSimpleMeta($eventName, $eventID, $data) {

    $data = [
      'name' => $eventName,
      'field_events_apiid' => $eventID,
      'field_properties' => json_encode($data),
    ];
    $node = \Drupal::entityTypeManager()
      ->getStorage('channels_by_contenttype')
      ->create($data);
    $node->save();
  }

  public function createMultipleMeta($eventID, $metaDescriptionArray, $metaName, $nodeGPID, $token) {
    foreach ($metaDescriptionArray as $metaArray) {
      foreach ($metaArray["meta"] as $array) {
        $data = [
          'name' => $metaName . $metaArray['id'] . ' - channel/' . $array['channel'],
          'field_cannel_apiid' => $array['channel'],
          'field_order_priority' => $metaArray['running_order'],
          'field_events_apiid' => $eventID,
          'field_meta_type_event_sport_part' => $metaName,
          'field_meta_type_api_id_event_id' => $metaArray['id'],
          'field_properties' => json_encode($array['properties']),
          'field_game_page_taxonomy' => $nodeGPID,
          'field_token' => $token,
        ];
        $node = \Drupal::entityTypeManager()
          ->getStorage('channels_by_contenttype')
          ->create($data);
        $node->save();
      }
    }
  }

  //-------------------------------------------------------- TAXONOMY CREATION ----------------------------


  public function createGenericTaxonomy($obj) {
    $term = Term::create($obj);
    $term->save();
    $term = reset($term);
    return $term;
  }

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
    echo ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
    $term = reset($taxonomy);
    return $term;

  }

  public function createParticipantTaxonomy($name, $voc, $idApi) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomy = $getInfoObj->getTaxonomyByParticipantAPIID($idApi);

    if (!$taxonomy or empty($taxonomy)) {
      $term = Term::create([
        'parent' => [],
        'name' => $name,
        'vid' => $voc,
        'field_participant_api_id' => $idApi,
      ]);
      $term->save();
      $taxonomy = $getInfoObj->getTaxonomyByParticipantAPIID($idApi);
    }
    echo ' Creating Participant Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
    return $taxonomy->id();

  }

  public function createTournametTaxonomy($competition, $voc, $sport_tags, $sport_id, $logo, $logo_modified) {
    $getInfoObj = new RepoGeneralGetInfo();
    $index = count($competition) - 1;
    $tournamentId = $competition[0][0]["id"];
    $tournamentName = $competition[0][0]["name"];
    $tournamentParent = $competition[0][0]["parent"];
    $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
    if (empty($taxonomy)) {
      if ($tournamentParent == NULL) {
        $data = $getInfoObj->getClearUrl(strtolower($tournamentName));
        $alias =  $getInfoObj->getMultiplesAlias($data);
        $obj = [
          'parent' => [$sport_tags],
          'name' => $tournamentName,
          'vid' => $voc,
          'field_api_id' => $tournamentId,
          'field_api_parent' => '',
          'field_sport_api_id' => $sport_id,
          'field_competition_id' => $tournamentId,
          'field_content' => '',
          'field_logo' => $logo,
          'field_logo_date' => $logo_modified,
          'field_sport_api_id' => $sport_tags,
          'path' => $alias,
        ];
        $this->createGenericTaxonomy($obj);
      }
      else {
        $competition = $getInfoObj->getTaxonomyParent($competition);
        $index = count($competition) - 1;
        for ($i = $index; $i >= 0; $i--) {
          $id = $competition[$i][0]["id"];
          $name = $competition[$i][0]["name"];
          $tournamentId = $competition[$i][0]["id"];
          $parent_id = $competition[$i][0]["parent"];
          $parentTaxonomyId = $competition[$i + 1][0]["id"];
          $taxonomy = $getInfoObj->getTaxonomyByAPIID($id);
          if (empty($taxonomy)) {
            if ($parent_id != NULL) {
              $parentTaxonomyId = $getInfoObj->getTaxonomyByAPIID($parentTaxonomyId);
              $parentTaxonomyId = $parentTaxonomyId->id();
              $data = $getInfoObj->getClearUrl(strtolower($name));
              $alias =  $getInfoObj->getMultiplesAlias($data);
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
                'field_logo_date' => $logo_modified,
                'field_sport_api_id' => $sport_tags,
                'path' => $alias,
              ];
              $this->createGenericTaxonomy($obj);
            }
            else {
              $data = $getInfoObj->getClearUrl(strtolower($name));
              $alias =  $getInfoObj->getMultiplesAlias($data);
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
                'field_logo_date' => $logo_modified,
                'field_sport_api_id' => $sport_tags,
                'path' => $alias,
              ];
              $this->createGenericTaxonomy($obj);
            }
          }

        }
      }
    }
    echo ' Creating Tournament Taxonomy - ' . $tournamentName . ' - at ' . date("h:i:s") . "\n";
    $tournamentID = $competition[0][0]["id"];
    $tournamentTaxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentID);
    return $tournamentTaxonomy->id();
  }

  public function defaultTournamentTaxonomy($parentId, $name, $voc, $tournamentId, $tournamentParents, $sport_id) {
    $getInfoObj = new RepoGeneralGetInfo();
    $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
    if ($taxonomy == FALSE) {
      $term = Term::create([
        'parent' => [$parentId],
        'name' => $name,
        'vid' => $voc,
        'field_api_id' => $tournamentId,
        'field_api_parent' => $tournamentParents,
        'field_sport_api_id' => $sport_id,
      ]);
      $term->save();
      echo ' Creating Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
      echo ' Creating Tournament Taxonomy -' . $name . ' - at ' . date("h:i:s") . "\n";
      $taxonomy = $getInfoObj->getTaxonomyByAPIID($tournamentId);
    }
    return $taxonomy->id();

  }

  public function createVocabulary() {
    $vocabularys = ['sport','stream_provider','participant','tournament'];
    foreach ($vocabularys as $vocal) {
      $vocabulary = \Drupal\taxonomy\Entity\Vocabulary::create([
        'vid' => $vocal,
        'description' => '',
        'name' => $vocal,
      ]);
      echo ' Creating Vocabulary -' . $vocal . ' - at ' . date("h:i:s") . "\n";
      $vocabulary->save();
    }
    return TRUE;

  }

  //-------------------------------------------------------- Generic CREATION ----------------------------

  public function createNodeGeneric($data) {
    $node = Node::create($data);
    $node->save();
    return $node->id();
  }

  public function createItemMenu($menu_name, $description, $nodeId, $sport, $sport_tags, $sport_id,$region) {
    $getIgetInfoObj = new RepoGeneralGetInfo();
    $id_node = \Drupal::entityTypeManager()
      ->getStorage('menu')
      ->loadByProperties(['id' => $menu_name]);
    if (!$id_node) {
      $menu = \Drupal::entityTypeManager()->getStorage('menu')->create([
        'id' => $menu_name,
        'label' => $menu_name,
        'menu_name' => $menu_name,
        'description' => $description,
      ])->save();
      echo " Creating Menu $menu_name" . ' - at ' . date("h:i:s") . "\n";
    }
    $menu_link = \Drupal::entityTypeManager()
      ->getStorage('menu_link_content')
      ->loadByProperties(['menu_name' => $menu_name, 'title' => $sport]);
    if (empty($menu_link)) {

        $gs = MenuLinkContent::create([
          'title' => $sport,
          'link' => ['uri' => 'internal:/node/' . $nodeId],
          'description' => $sport_tags,
          'menu_name' => $menu_name,
          'parent' => 'null',
          'expanded' => TRUE,
        ])->save();


        $menu_link_SportObj = \Drupal::entityTypeManager()
          ->getStorage('menu_link_content')
          ->loadByProperties(['menu_name' => $menu_name, 'title' => $sport,]);

        $uuid = reset($menu_link_SportObj)->uuid->value;
          if ($uuid) {
            $menuID = 'main';
            $forside = $sport . ' Forside';
            $this->createGenericItemMenu($menuID, $forside, $nodeId, $uuid, -99);

            $LiveStream = 'Live Stream ' . $sport;
            $url = 'Live Stream ' . $sport;
            $id = 'liveStream';
            $LiveStream_id = $this->createSportInternPages($sport_tags, $sport, $LiveStream, $id, $url, $sport_id, 'sport_stream_reviews',$region);
            $this->createGenericItemMenu($menuID, $LiveStream, $LiveStream_id, $uuid, -98);

            $Blog = 'Blog ' . $sport;
            $url = 'Blog';
            $id = 'blog';
            $Blog_id = $this->createSportInternBlog($sport_tags, $sport, $Blog, $id, $url, $sport_id, 'sport_blogs',$region);
            $this->createGenericItemMenu($menuID, $Blog, $Blog_id, $uuid, -97);
          }
          echo " Creating menuitem $sport on Menu $menu_name" . ' - at ' . date("h:i:s") . "\n";
    }
    return TRUE;
  }

  public function createGenericItemMenu($menuID, $title, $nodeID, $parent, $weight) {
    MenuLinkContent::create([
      'title' => $title,
      'link' => ['uri' => 'internal:/node/' . $nodeID],
      'menu_name' => $menuID,
      'expanded' => TRUE,
      'parent' => 'menu_link_content:' . $parent,
      'weight' => $weight,
    ])->save();
    echo " Creating menu item '. $title . ' on Menu" . ' - at ' . date("h:i:s") . "\n";
    return TRUE;
  }

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
        echo " Creating Menu $menu_name[$i]" . ' - at ' . date("h:i:s") . "\n";
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
        echo " Creating menu item $sport on Menu $menu_name[$i]" . ' - at ' . date("h:i:s") . "\n";
      }
    }
    return TRUE;
  }

  public function createSportPages_multiplesMenu($sport_id) {
    $rpClient = RPAPIClient::getClient();
    $para = ['id' => $sport_id];
    $getInfoObj = new RepoGeneralGetInfo();
    $node_id = $getInfoObj->getNode($sport_id, 'sport_pages', 'field_sport_api_id');
    if (empty($node_id)) {
      $competition = $rpClient->getSportbyID($para);
      $name = $competition["name"];
      $id_api = $competition["id"];
      $node = [
        'type' => 'sport_pages',
        'title' => $name,
        'field_sport_name' => $name,
        'field_sport_api_id' => $id_api,
        'field_sport_theme_descrption' => $getInfoObj->getDefaultSportPage($name),
        'path' => ['alias' => '/' . strtolower($name)],
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
      echo ' Creating Sport Pages -' . $name . '- at ' . date("h:i:s") . "\n";
    }
    else {
      $competition = $rpClient->getSportbyID($para);
      $name_sport = $competition["name"];
      $term = $getInfoObj->getTaxonomy($name_sport);
      $sport_tags = reset($term)->id();
      echo 'Get Sport Pages Taxonomy-' . $name_sport . '- at ' . date("h:i:s") . "\n";
    }
    return $sport_tags;
  }

  public function CreateClientData() {
  }

}
