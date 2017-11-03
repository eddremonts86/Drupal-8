<?php

namespace Drupal\rp_cms_steve_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Class SteveBaseControler.
 */
abstract class SteveBaseControler extends ControllerBase {

  /**
   * __construct.
   * Change $_SESSION["channel"]
   * Default Channel - Organic - No 3
   *
   */
  public function __construct() {
    if (!isset($_SESSION["channel"]) or $_SESSION["channel"] == NULL) {
      $_SESSION["channel"] = '3';
    }
    if ($_GET["channel"] != $_SESSION["channel"] and isset($_GET["channel"])) {
      session_start();
      $_SESSION["channel"] = $_GET['channel'];
    }

  }

  /*------------- Taxonomy ------------*/

  public function getTaxonomyByCriterio($obj, $reset = 0) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($obj);
    if ($reset != 0) {
      return $taxonomy;
    }
    else {
      return reset($taxonomy);
    }
  }

  public function getTaxonomyAlias($id) {
    $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $id])
      ->toString();
    return $url;
  }

  /*------------- Nodes ------------*/

  public function getNodeAlias($id) {
    $url = Url::fromRoute('entity.node.canonical', ['node' => $id])->toString();
    return $url;
  }

  public function getNodeByCriterio($obj) {
    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties($obj);
    return $node;
  }

  public function getNodeByUrl($toArray = 0) {
    $nodes = \Drupal::routeMatch()->getParameter('node');
    if (!isset($nodes) or $nodes == NULL) {
      return TRUE;
    }
    else {
      if ($toArray == 0) {
        $node = $nodes->toArray();
        return $node;
      }
      else {
        return $nodes;
      }

    }


  }

  /*------------- Images ------------*/

  public function getImgUrl($id) {
    $imgUrl = '';
    if (isset($id) and $id != NULL and $id != '') {
      $img = File::load($id)->toArray();
      $imgUrl = $img["uri"][0]["value"];
    }
    return $imgUrl;
  }

  public function getSchedule($range) {
    $fromDate = strtotime(date('Y-m-d'));
    $sport = $this->getSport();
    $obj = [
      'vid' => 'channels',
      'field_channel_api_id' => $_SESSION['channel'],
    ];
    $channel = $this->getTaxonomyByCriterio($obj, 0);
    $channelid = $channel->id();
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_events_sport', $sport['sportDrupalId'])
      ->condition('field_event_date', $fromDate, '>')
      ->condition('field_event_channels', $channelid, '=')
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    if ($range != 0) {
      $ids->range(0, $range);
    }
    $all_nodes = $this->getNodes($ids->execute());
    return $this->getScheduleFormat($all_nodes);
  }

  public function getScheduleLiveStreamEvents($range) {
    $fromDate = strtotime(date('Y-m-d'));
    $sport = $this->getSport();
    $obj = [
      'vid' => 'channels',
      'field_channel_api_id' => $_SESSION['channel'],
    ];
    $channel = $this->getTaxonomyByCriterio($obj, 0);
    $channelid = $channel->id();
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_events_sport', $sport['sportDrupalId'])
      ->condition('field_event_date', $fromDate, '>')
      ->condition('field_event_channels', $channelid, '=')
      ->condition('field_event_promotetolivestream', $channelid, '=')
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    if ($range != 0) {
      $ids->range(0, $range);
    }
    $all_nodes = $this->getNodes($ids->execute());
    return $this->getScheduleFormat($all_nodes);
  }

  public function getSchedulePager($page) {
    $fromDate = strtotime(date('Y-m-d'));
    $sport = $this->getSport();
    $obj = [
      'vid' => 'channels',
      'field_channel_api_id' => $_SESSION['channel'],
    ];
    $channel = $this->getTaxonomyByCriterio($obj, 0);
    $channelid = $channel->id();
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_events_sport', $sport['sportDrupalId'])
      ->condition('field_event_date', $fromDate, '<=')
      ->condition('field_event_channels', $channelid, '=')
      ->pager($page)
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    $all_nodes = $this->getNodes($ids->execute());
    return $this->getScheduleFormat($all_nodes);
  }

  public function getScheduleFormat($nodeList) {
    $newNodeList = [];
    foreach ($nodeList as $simpleNode) {
      $nid = $simpleNode['nid'][0]['value'];
      $nodeAlias = $this->getNodeAlias($nid);
      $uuid = $simpleNode['uuid'][0]['value'];
      $vid = $simpleNode['vid'][0]['value'];
      $langCode = $simpleNode['langcode'][0]['value'];
      $status = $simpleNode['status'][0]['value'];
      $title = $simpleNode['title'][0]['value'];
      $properties = $simpleNode['field_events_properties'][0]['value'];
      $nidAPI = $simpleNode['field_event_api_id'][0]['value'];
      $date = $simpleNode['field_event_date'][0]['value'];
      $sportId = $simpleNode['field_events_sport'][0]['target_id'];
      $tournamentId = $simpleNode['field_event_tournament'][0]['target_id'];
      $tournament = $this->getTaxonomyByCriterio([
        'vid' => 'sport',
        'tid' => $tournamentId,
      ], 0);
      $tournamentLogo = $this->getImgUrl($tournament->field_logo->target_id);
      $participantsList = $simpleNode['field_event_participants'];
      $participantsListformat = $this->getParticipant($participantsList);
      $eventStreams = $this->getStreamEventList($simpleNode['field_event_stream_provider']);
      $sportName = $this->getSport();

      $newNodeList[] = [
        'nodeId' => $nid,
        'uuid' => $uuid,
        'vid' => $vid,
        'langcode' => $langCode,
        'status' => $status,
        'title' => $title,
        'properties' => $properties,
        'nidAPI' => $nidAPI,
        'eventDate' => $date,
        'eventAlias' => $nodeAlias,
        'sportid' => $sportId,
        'sportname' => $sportName['sportName'],
        'sportBG' => $sportName['sportBackground'],
        'sportalias' => $this->getTaxonomyAlias($sportId),
        'eventTournamentID' => $tournamentId,
        'eventStreams' => $eventStreams,
        'TournamentAlias' => $this->getTaxonomyAlias($tournamentId),
        'eventTournamentAPIID' => $tournament->field_api_id->value,
        'eventTournamentName' => $tournament->name->value,
        'eventTournamentLogo' => $tournamentLogo,
        'participantsList' => $participantsListformat,
      ];
    }

    return $newNodeList;

  }

  public function getParticipant($participantsList) {
    $participantsListFormat = [];
    foreach ($participantsList as $participants) {
      if ($participants["target_id"]) {
        $tournamentContent = $this->getTaxonomyByCriterio([
          'vid' => 'participant',
          'tid' => $participants["target_id"],
        ], 0);

        $name = $tournamentContent->name->value;
        $id = $tournamentContent->tid->value;
        $idAPI = $tournamentContent->field_participant_api_id->value;
        $logo = $tournamentContent->field_participant_logo->target_id;
        $participantsListFormat[] = [
          'idAPI' => $idAPI,
          'logo' => $this->getImgUrl($logo),
          'name' => $name,
          'participantAlias' => $this->getTaxonomyAlias($id),
        ];
      }
    }
    return $participantsListFormat;

  }

  public function getNodes($ids) {
    $all_nodes = [];
    foreach ($ids as $id) {
      $node = Node::load($id);
      $all_nodes [] = $node->toArray();
    }
    return $all_nodes;
  }

  public function getSchedulePlusTree($range = 0, $format = "Y-m-d") {
    $nodes = $this->getSchedule($range);
    $tree = $this->getTree($nodes, $format);
    return $tree;
  }

  public function getTree($data, $format) {
    $tree['AllEvents'] = [];
    foreach ($data as $event) {
      $date = date($format, $event['eventDate']);
      $league = $event['eventTournamentName'];
      $tournament_id = $event['eventTournamentID'];
      $tournamentAPIID = $event['eventTournamentAPIID'];
      $url = $event['eventTournamentLogo'];
      if (!(@$tree['AllEvents'][$date])) {
        $tree['AllEvents'][$date] = ['date' => $date];
        $tree['AllEvents'][$date]['alltournament'][$league]['events'] = [];
        $tree['AllEvents'][$date]['alltournament'][$league]['tournament'] = $league;
        $tree['AllEvents'][$date]['alltournament'][$league]['tournament_id'] = $tournament_id;
        $tree['AllEvents'][$date]['alltournament'][$league]['tournamentIMG'] = $url;
        $tree['AllEvents'][$date]['alltournament'][$league]['eventTournamentIDAcoordion'] = $this->getClearUrl($date . $league . $tournament_id);
        array_push($tree['AllEvents'][$date]['alltournament'][$league]['events'], $event);
      }
      else {
        if (@$tree['AllEvents'][$date]) {
          if (!(@$tree['AllEvents'][$date]['alltournament'][$league]) and (@$tree['AllEvents'][$date][$league]['tournament_id']) != $tournament_id) {
            $tree['AllEvents'][$date]['alltournament'][$league]['tournament'] = $league;
            $tree['AllEvents'][$date]['alltournament'][$league]['tournament_id'] = $tournament_id;
            $tree['AllEvents'][$date]['alltournament'][$league]['eventTournamentIDAcoordion'] = $this->getClearUrl($date . $league . $tournament_id);
            $tree['AllEvents'][$date]['alltournament'][$league]['tournamentIMG'] = $url;
            $tree['AllEvents'][$date]['alltournament'][$league]['events'] = [];
            array_push($tree['AllEvents'][$date]['alltournament'][$league]['events'], $event);
          }
          else {
            array_push($tree['AllEvents'][$date]['alltournament'][$league]['events'], $event);
          }

        }
      }
    }
    return $tree;
  }

  /*------------- Sports ------------*/
  public function getSport() {
    $nodeObj = \Drupal::routeMatch()->getParameter('node');
    if (isset($nodeObj) and $nodeObj != NULL) {
      $node = $nodeObj->toArray();
      $type = $node["type"][0]["target_id"];
      $sportTaxonomyId = $node['field_' . $type . '_sport'][0]["target_id"];
      $data = ['tid' => $sportTaxonomyId, 'vid' => 'sport'];
      $sportObj = $this->getTaxonomyByCriterio($data);
      $sportbackground = @$this->getImgUrl($sportObj->field_background->target_id);
      $sportbackground = (isset($sportbackground)) ? $sportbackground : '';
      $sportObjFormnat = [
        'sportDrupalId' => $sportObj->id(),
        'sportName' => $sportObj->name->value,
        'sportBackground' => $sportbackground,
      ];
      return $sportObjFormnat;
    }
    return [];
  }

  public function getClearUrl($s) {
    $s = trim($s, "\t\n\r\0\x0B");

    //--- Latin ---//
    $s = str_replace('ü', 'u', $s);
    $s = str_replace('Á', 'A', $s);
    $s = str_replace('á', 'a', $s);
    $s = str_replace('é', 'e', $s);
    $s = str_replace('É', 'E', $s);
    $s = str_replace('í', 'i', $s);
    $s = str_replace('Í', 'I', $s);
    $s = str_replace('ó', 'o', $s);
    $s = str_replace('Ó', 'O', $s);
    $s = str_replace('Ú', 'U', $s);
    $s = str_replace('ú', 'u', $s);

    //--- Nordick ---//
    $s = str_replace('ø', 'o', $s);
    $s = str_replace('Ø', 'O', $s);
    $s = str_replace('Æ', 'E', $s);
    $s = str_replace('æ', 'e', $s);
    $s = str_replace('Å', 'A', $s);
    $s = str_replace('å', 'a', $s);

    //--- Others ---//
    $s = str_replace(' - ', '-vs-', $s);
    $s = str_replace(' ', '_', $s);
    $s = str_replace('.', '_', $s);
    $s = str_replace('\"', '_', $s);
    $s = str_replace(':', '_', $s);
    $s = str_replace(',', '_', $s);
    $s = str_replace(';', '_', $s);
    $s = str_replace('/', '_', $s);

    $s = strtolower($s);
    $s = trim($s, "\t\n\r\0\x0B");
    return $s;
  }


  /*------------- Stream ------------*/
  public function getStreamList() {
    $sport = $this->getSport();
    if (!empty($sport)) {
      $obj = [
        'vid' => 'stream_provider',
        'field_stream_sport_promote' => $sport['sportDrupalId'],
      ];
      $list = $this->getTaxonomyByCriterio($obj, 1);
      return $list;
    }
    else {
      return [];
    }
  }

  public function getStreamListFormat($eventstreamList = []) {
    if (empty($eventstreamList)) {
      $list = $this->getStreamList();
    }
    else {
      foreach ($eventstreamList as $eventstream) {
        if ($eventstream["target_id"]) {
          $obj = [
            'vid' => 'stream_provider',
            'tid' => $eventstream["target_id"],
          ];
          $list [] = $this->getTaxonomyByCriterio($obj, 0);
        }
      }
    }
    $listFormat = [];
    foreach ($list as $listF) {
      $streamIMG = @$listF->field_streamprovider_logo->target_id;
      $streamIMGAlt = @$listF->field_streamprovider_logo->target_id;
      if (isset($streamIMG) and $streamIMG != '' and $streamIMG != NULL) {
        $streamIMG = $this->getImgUrl($streamIMG);
      }
      else {
        $streamIMG = 'https://images-na.ssl-images-amazon.com/images/I/61I7WFEiORL.png';
        $streamIMGAlt = $listF->name->value;
      }
      $listFormat[] = [
        'id' => $listF->id(),
        'streamName' => $listF->name->value,
        'apiId' => $listF->field_stream_provider_api_id->value,
        'homePromo' => $listF->field_stream_provider_home_promo->value,
        'description' => $listF->description->value,
        'idTabsTemplate' => $this->getClearUrl($listF->name->value . '_' . $listF->field_stream_provider_api_id->value),
        /**/
        'streamRating' => 3,
        'streamPrice' => '$12',
        'streamVideoQuality' => 'good',
        'streamVideoSize' => 'Big',
        'endLink' => 'http://google.com',
        'streamIMG' => $streamIMG,
        'streamIMGAlt' => $streamIMGAlt,
        'sport' => $this->getSport(),
      ];
    }
    return $listFormat;

  }

  public function getStreamEventList($eventstreamList) {
    $streamListFormat = $this->getStreamListFormat($eventstreamList);
    return $streamListFormat;
    /*  foreach ($eventstreamList as $eventstream) {
        if ($eventstream["target_id"]) {
          $obj = [
            'vid' => 'stream_provider',
            'tid' => $eventstream["target_id"],
          ];
          $listF = $this->getTaxonomyByCriterio($obj, 0);
          $streamIMG = @$listF->field_streamprovider_logo->target_id;
          $streamIMGAlt = @$listF->field_streamprovider_logo->target_id;
          if (isset($streamIMG) and $streamIMG != '' and $streamIMG != NULL) {
            $streamIMG = $this->getImgUrl($streamIMG);
          }
          else {
            $streamIMG = 'https://images-na.ssl-images-amazon.com/images/I/61I7WFEiORL.png';
          }
          $streamListFormat[] = [
            'id' => $listF->id(),
            'streamName' => $listF->name->value,
            'apiId' => $listF->field_stream_provider_api_id->value,
            'homePromo' => $listF->field_stream_provider_home_promo->value,
            'description' => $listF->description->value,
            'idTabsTemplate' => $this->getClearUrl($listF->name->value . '_' . $listF->field_stream_provider_api_id->value),
            /**
            'streamRating' => 3,
            'streamPrice' => '$12',
            'streamVideoQuality' => 'good',
            'streamVideoSize' => 'Big',
            'endLink' => 'http://google.com',
            'streamIMG' => $streamIMG,
            'streamIMGAlt' => $streamIMGAlt,

          ];
        }
      }
      return $streamListFormat;*/
  }


  /*------------------Tools--------------------------*/
  public function getPHP_Var_Dump($data, $type = 0) {
    echo "<div class='container' style='background: rgba(201, 201, 201, 0.55);color: #212121;padding: 20px;font-size: 15px;border-radius: 10px;height: auto;width: auto;'> <pre>";

    if ($type == 0) {
      var_dump($data);
    }
    else {
      if ($type == 1) {
        $data = count($data);
        var_dump($data);
      }
      else {
        var_dump($data);
      }
    }
    echo "</pre></div>";
    exit();
  }

}
