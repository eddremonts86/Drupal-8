<?php

namespace Drupal\rp_cms_steve_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;


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
    $session = @$_SESSION["channel"];
    $channel = @$_GET["channel"];
    if ($session == NULL or $session == '' or !isset($session)) {

      $config = \Drupal::configFactory()->get('rp_base.settings');
      $site_def_channel = $config->get('rp_base_def_channel');
      $_SESSION["channel"] = $site_def_channel;
    }
    else {
      if ($channel != $session and isset($channel)) {
        $_SESSION["channel"] = $channel;
      }
    }
    $_SESSION["region"] = strval(\Drupal::languageManager()
      ->getCurrentLanguage()
      ->getId());
    $_SESSION["defRegion"] = strval(\Drupal::languageManager()
      ->getDefaultLanguage()
      ->getId());

  }

  public function getCountry() {
    $DefaultLanguage = \Drupal::languageManager()
      ->getDefaultLanguage()
      ->getId();
    $CurrentLanguage = \Drupal::languageManager()
      ->getCurrentLanguage()
      ->getId();
    //$languageIP=  geoip_country_code_by_name($_SERVER["REMOTE_ADDR"]);
    $request = Request::createFromGlobals();
    $Land = $request->cookies->get("Land");

    if ($Land) {
      $language = $Land;
    }
    else {
      $request->cookies->set('Land', $CurrentLanguage);
      $language = $CurrentLanguage;
    }

    if ($CurrentLanguage != $Land) {
      $request->cookies->remove('Land');
    }
    return $language;
  }

  public function getChannel() {
    $session = @$_SESSION["channel"];
    $channel = @$_GET["channel"];
    if ($session == NULL or $session == '' or !isset($session)) {
      return 1;
    }
    else {
      if ($channel != $session and isset($channel)) {
        return $channel;
      }
    }
  }

  public function getBreadcrumbs() {
    $route = \Drupal::routeMatch();
    $routeName = $route->getRouteName();
    $currentUrl = \Drupal::request()->getRequestUri();

    $links = [$this->breadcrumbLink('Home', '/')];


    switch ($routeName) {
      case 'entity.node.canonical':
        $page = $route->getParameter('node');
        $sport = $this->getSport();

        if ($sport) {
          $nodes = $this->getNodeByCriterio(['type' => 'sport'], 1);
          foreach ($nodes as $node) {
            $t[] = $node->field_sport_sport->target_id;
            if ($node->field_sport_sport->target_id == $sport['sportDrupalId']) {
              $links[] = $this->breadcrumbLink($node->title->value, $this->getNodeAlias($node->id()));
              break;
            }
          }

          if ($page->type->target_id != 'sport') {
            $links[] = $this->breadcrumbLink($page->title->value, $currentUrl);
          }
        }
        else {
          $links[] = $this->breadcrumbLink($page->title->value, $this->getNodeAlias($page->id()));
        }

        break;
      case 'entity.taxonomy_term.canonical':
        $term = $this->getTaxonomyTermByUrl();
        $links[] = $this->breadcrumbLink($term->name->value, $currentUrl);
        break;
      case 'rp_cms_steve_integration.live_stream_reviews':
        $links[] = $this->breadcrumbLink('Stream Reviews', $currentUrl);
        break;
      case 'rp_cms_steve_integration.live_stream_review':
        $term = $this->getTaxonomyTermByUrl();
        $links[] = $this->breadcrumbLink('Stream Reviews', '/live-stream-reviews');
        $links[] = $this->breadcrumbLink($term->name->value, $currentUrl);
        break;
      default:
        $request = \Drupal::request();
        $pageTitle = \Drupal::service('title_resolver')
          ->getTitle($request, $route->getRouteObject());
        $links[] = $this->breadcrumbLink($pageTitle, $currentUrl);
        break;
    }

    return $links;
  }

  private function breadcrumbLink($name, $url) {
    return [
      'name' => $name,
      'url' => $url,
    ];
  }

  /*------------- Taxonomy ------------*/

  public function getSport($nodeid = NULL) {

    if (isset($nodeid) and $nodeid != NULL) {
      $entity = Node::load($nodeid);
    }
    else {
      if ($entity = $this->getNodeByUrl(1)) {
        $entityType = 'node';
      }
      else {
        if ($entity = $this->getTaxonomyTermByUrl()) {
          $entityType = 'taxonomy';
        }
      }
    }

    if (isset($entity) && $entity != NULL) {
      $sportTaxonomyId = NULL;

      if ($entityType == 'node') {
        $sportTaxonomyId = $entity->{'field_' . $entity->type->target_id . '_sport'}->target_id;
      }
      else {
        if ($entityType == 'taxonomy') {
          if ($entity->getVocabularyId() == 'sport') {
            if ($entity->field_sport_api_id->target_id != NULL) {
              $sportTaxonomyId = $entity->field_sport_api_id->target_id;
            }
            else {
              $sportTaxonomyId = $entity->id();
            }
          }
          else {
            if ($entity->{'field_' . $entity->getVocabularyId() . '_sport'}->target_id) {
              $sportTaxonomyId = $entity->{'field_' . $entity->getVocabularyId() . '_sport'}->target_id;
            }
          }
        }
      }
      if ($sportTaxonomyId) {
        $data = ['tid' => $sportTaxonomyId, 'vid' => 'sport'];
        $sportObj = $this->getTaxonomyByCriterio($data);
        $sportbackground = @$this->getImgUrl($sportObj->field_background->target_id);
        $sportbackground = (isset($sportbackground)) ? $sportbackground : '';
        $sportObjFormnat = [
          'sportDrupalId' => $sportObj->id(),
          'sportName' => $sportObj->name->value,
          'sportBackground' => $sportbackground,
          'sportColor' => (isset($sportObj->field_base_color->value) ? $sportObj->field_base_color->value : NULL),
        ];
        return $sportObjFormnat;
      }
    }
    return [];
  }

  public function getNodeByUrl($toArray = 0) {
    $nodes = \Drupal::routeMatch()->getParameter('node');
    if (!isset($nodes)) {
      return FALSE;
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

  /*------------- Nodes ------------*/

  public function getTaxonomyTermByUrl() {
    $routeName = \Drupal::routeMatch()->getRouteName();
    if ($routeName == 'entity.taxonomy_term.canonical') {
      $tid = \Drupal::routeMatch()->getRawParameter('taxonomy_term');
      $term = \Drupal\taxonomy\Entity\Term::load($tid);
      return $term;
    }
    else {
      if ($routeName == 'rp_cms_steve_integration.live_stream_review') {
        $tid = \Drupal::routeMatch()->getRawParameter('term');
        $term = \Drupal\taxonomy\Entity\Term::load($tid);
        return $term;
      }
    }
    return NULL;
  }

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

  public function getImgUrl($id) {
    $imgUrl = '';
    if (isset($id) and $id != NULL and $id != '') {
      $img = File::load($id)->toArray();
      $imgUrl = $img["uri"][0]["value"];
    }
    return $imgUrl;
  }


  /*------------- Images ------------*/

  public function getNodeByCriterio($obj, $reset = 0) {
    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties($obj);
    if ($reset != 0) {
      return $node;
    }
    else {
      return reset($node);
    }
  }

  public function getNodeAlias($id, $absolute = FALSE) {
    $options = [];
    if ($absolute) {
      $options['absolute'] = TRUE;
    }

    $url = Url::fromRoute('entity.node.canonical', ['node' => $id], $options)
      ->toString();
    return $url;
  }

  public function getSchedulePlusTree($range = 0, $format = "Y-m-d") {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $dateSchedule = $config->get('rp_base_scheduleDays');

    $nodes = $this->getSchedule($range,$dateSchedule);
    if ($nodes) {
      $tree = $this->getTree($nodes, $format);
    }
    else {
      $tree = $this->getTournamentList();
    }

    return $tree;
  }

  public function getSchedule($range, $days = 7, $round = 0) {
    set_time_limit(120);
    $date = date('Y-m-d');
    $fromDate = strtotime($date);
    $endDate = strtotime('+' . $days . ' day', strtotime($date));
    $sport = $this->getSport();
    $channel = $this->getChannelTaxonomyId();
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_events_sport', $sport['sportDrupalId'])
      ->condition('field_event_date', $fromDate, '>')
      ->condition('field_event_date', $endDate, '<')
      ->condition('field_event_channels', $channel, '=')
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    if ($range != 0) {
      $ids->range(0, $range);
    }
    $all_nodes = $this->getNodes($ids->execute());
    $nodelist = $this->getScheduleFormat($all_nodes);

    if (count($nodelist) < $range and $round < 5 and $range != 0) {
      if ($round >= 1 and count($nodelist) == ($range - 1)) {
        return $nodelist;
      }
      $round++;
      $nodelist = $this->getSchedule(($round * 5), $days = 3, $round);
      return $nodelist;
    }

    return $nodelist;
  }


  public function getChannelTaxonomyId() {
    $obj = [
      'vid' => 'channels',
      'field_api_id' => $_SESSION['channel'],
    ];
    $channel = $this->getTaxonomyByCriterio($obj, 0);
    if ($channel) {
      $channelTaxonomyId = $channel->id();
      return $channelTaxonomyId;
    }
    else {
      $obj = [
        'vid' => 'channels',
        'field_api_id' => 3,
      ];
      $channel = $this->getTaxonomyByCriterio($obj, 0);
      if ($channel) {
        $channelTaxonomyId = $channel->id();
        return $channelTaxonomyId;
      }
    }
  }

  public function getNodes($ids, $rest = FALSE) {
    $all_nodes = [];
    if ($_SESSION["region"] == $_SESSION["defRegion"]) {
      foreach ($ids as $id) {
        $node = Node::load($id);
        if (!$rest) {
          $all_nodes [] = $node->toArray();
        }
        else {
          $all_nodes [] = $node;
        }
      }
    }
    else {
      foreach ($ids as $id) {
        $node = Node::load($id);
        $node_trans = \Drupal::service('entity.repository')
          ->getTranslationFromContext($node, $_SESSION["region"]);
        if (!$rest) {
          $all_nodes [] = $node_trans->toArray();
        }
        else {
          $all_nodes [] = $node_trans;
        }
      }
    }
    return $all_nodes;
  }

  public function getScheduleFormat($nodeList) {
    $newNodeList = [];
    foreach ($nodeList as $simpleNode) {
      $properties = $simpleNode['field_events_properties'][0]['value'];
      $participantPropertiesArray = $data = \GuzzleHttp\json_decode($properties);
      $region = $_SESSION["region"];
      $participantPropertiesArray = $participantPropertiesArray->$region;

      $nid = $simpleNode['nid'][0]['value'];
      $nodeAlias = $this->getNodeAlias($nid);
      $uuid = $simpleNode['uuid'][0]['value'];
      $vid = $simpleNode['vid'][0]['value'];
      $langCode = $simpleNode['langcode'][0]['value'];
      $status = $simpleNode['status'][0]['value'];
      $title = $simpleNode['title'][0]['value'];

      $nidAPI = $simpleNode['field_event_api_id'][0]['value'];
      $date = $simpleNode['field_event_date'][0]['value'];
      $sportId = $simpleNode['field_events_sport'][0]['target_id'];
      $tournamentId = $simpleNode['field_event_tournament'][0]['target_id'];
      $tournament = $this->getTaxonomyByCriterio([
        'vid' => 'sport',
        'tid' => $tournamentId,
      ], 0);
      $tournamentLogo = $this->getImgUrl($tournament->field_logo->target_id);
      $leage_weight = $tournament->field_weight->value ? $tournament->field_weight->value : 1000;
      $participantsList = $simpleNode['field_event_participants'];
      $participantsListformat = $this->getParticipant($participantsList, $participantPropertiesArray);
      $eventStreams = $this->getStreamEventList($simpleNode['field_event_stream_provider']);

      $sportName = $this->getTaxonomyByCriterio([
        'vid' => 'sport',
        'tid' => $sportId,
      ], 0);


      $participantsWeight = 0;
      foreach ($participantsListformat as $participant) {
        $participantsWeight += $participant['participantsWeight'];
      }
      $promoted = $simpleNode['field_promoted_schedule_top'][0]['value'] ? TRUE : FALSE;
      $eventWeight = $simpleNode['field_promoted_schedule_top'][0]['value'] ? -1 : $participantsWeight;
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
        'fullEventUrl' => $this->getNodeAlias($nid, TRUE),
        'sportid' => $sportId,
        'sportname' => $sportName->name->value,
        'sportBG' => $sportName->field_base_color->value,
        'sportalias' => $this->getTaxonomyAlias($sportId),
        'eventTournamentID' => $tournamentId,
        'eventStreams' => $eventStreams,
        'TournamentAlias' => $this->getTaxonomyAlias($tournamentId),
        'eventTournamentAPIID' => $tournament->field_api_id->value,
        'eventTournamentName' => $tournament->name->value,
        'eventTournamentLogo' => $tournamentLogo,
        'order' => $eventWeight,
        'tournamentWeight' => $leage_weight,
        'participantsList' => $participantsListformat,
        'promoted' => $promoted,
      ];

    }
    return $newNodeList;
  }


  /* getOrderByPrioryty ()
   *
   *
   * Options:
   *    SORT_DESC
   *     SORT_ASC
   *
   * */
  /*------------- Menu ------------*/

  public
  function getParticipant($participantsList, $participantPropertiesArray = []) {
    $participantsListFormat = [];
    foreach ($participantsList as $participants) {
      if ($participants["target_id"]) {
        $tournamentContent = $this->getTaxonomyByCriterio([
          'vid' => 'participant',
          'tid' => $participants["target_id"],
        ], 0);
        $name = $tournamentContent->name->value;
        $id = $tournamentContent->tid->value;
        $idAPI = $tournamentContent->field_api_id->value;
        $participants_weight = $tournamentContent->field_weight->value ? $tournamentContent->field_weight->value : 100;
        $logo = $tournamentContent->field_participant_logo->target_id;
        $participantsListFormat[] = [
          'id' => $id,
          'idAPI' => $idAPI,
          'logo' => $this->getImgUrl($logo),
          'name' => $name,
          'participantAlias' => $this->getTaxonomyAlias($id),
          'order' => 1000,
          'participantsWeight' => $participants_weight,
        ];
      }
    }

    if ($participantPropertiesArray) {
      $newParticipantArray = [];


      if (is_object($participantPropertiesArray)) {
        $data = $participantPropertiesArray->participants;
      }
      else {
        if (isset($participantPropertiesArray[0]["value"])) {
          $data = \GuzzleHttp\json_decode($participantPropertiesArray[0]["value"]);
          $region = $_SESSION["region"];
          $data = $data->$region->participants;
        }
      }
      foreach ($participantsListFormat as $participant) {
        foreach ($data as $participantProperties) {
          if ($participant['idAPI'] == $participantProperties->id) {
            $participant['order'] = $participantProperties->running_order;
            $newParticipantArray [] = $participant;
            break;
          }
        }
      }
      $newlist = $this->getArraySort($newParticipantArray, 'order', SORT_ASC);
      return $newlist;
    }
    return $participantsListFormat;
  }

  public
  function getTaxonomyAlias($id) {
    $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $id])
      ->toString();
    return $url;
  }

  /*------------- Stream ------------*/

  public
  function getArraySort($array, $on, $order = SORT_ASC) {

    $new_array = [];
    $sortable_array = [];

    if (count($array) > 0) {
      foreach ($array as $k => $v) {
        if (is_array($v)) {
          foreach ($v as $k2 => $v2) {
            if ($k2 == $on) {
              $sortable_array[$k] = $v2;
            }
          }
        }
        else {
          $sortable_array[$k] = $v;
        }
      }

      switch ($order) {
        case SORT_ASC:
          asort($sortable_array);
          break;
        case SORT_DESC:
          arsort($sortable_array);
          break;
      }

      foreach ($sortable_array as $k => $v) {
        $new_array[$k] = $array[$k];
      }
    }

    return $new_array;
  }

  public
  function getStreamEventList($eventstreamList) {
    $streamListFormat = $this->getStreamListFormat($eventstreamList);
    return $streamListFormat;
  }

  public
  function getStreamListFormat($eventstreamList = []) {
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
        'apiId' => $listF->field_api_id->value,
        'homePromo' => $listF->field_stream_provider_home_promo->value,
        'sponsor' => $listF->field_stream_provider_sponsor->value,
        'description' => $this->getShortcode($listF->description->value),
        'idTabsTemplate' => $this->getClearUrl($listF->name->value . '_' . $listF->field_api_id->value),
        'streamIMG' => $streamIMG,
        'streamIMGAlt' => $streamIMGAlt,
        'sport' => $this->getSport(),
        'FrontPageButtonsReviewButton' => $this->getTaxonomyAlias($listF->id()),
        /* example data*/
        'streamRating' => rand(3, 5),
        'streamPrice' => '$' . rand(25, 115),
        'streamVideoQuality' => 'good',
        'streamVideoSize' => 'Big',
        'endLink' => 'http://google.com',

        'step1' => $this->getImgUrl($listF->field_step_1->target_id),
        'step2' => $this->getImgUrl($listF->field_step_2->target_id),
        'step3' => $this->getImgUrl($listF->field_step_3->target_id),
        'step_lvs' => $this->getImgUrl($listF->field_step_lvs->target_id),

        'FrontPageQualityHeading' => 'Quality ',
        'FrontPageQualityContent' => 'As you expect from this leading bookmaker, sound and image quality are top notch, ensuring a seamless live streaming experience.',

        'FrontPageUdvalgHeading' => 'Selection ',
        'FrontPageUdvalgContent' => 'bet365 shows 100,000+ events every year and that includes plenty of football of course.',

        'FrontPagePriceHeading' => 'Price ',
        'FrontPagePriceContent' => 'All that\'s required to watch is a funded account (one with money in) or for you to have placed a bet in the last 24 hours.',


        'FrontPageIcons3note' => '* You need to be logged in and have a funded account or to have placed a bet in the last 24 ',

        'FrontPageIcons1Heading' => 'Go to the Website',
        'FrontPageIcons1Subheading' => 'Click here and go to the bet365 website',

        'FrontPageIcons2Heading' => 'Register an Account',
        'FrontPageIcons2Subheading' => 'Set up your account with bet365 and log in',

        'FrontPageIcons3Heading' => 'Watch Live',
        'FrontPageIcons3Subheading' => 'Go to the “Live Streaming” section and watch the game*',


        'FrontPageButtonsReviewButton' => '',
        'FrontPageButtonsReviewButtonText' => 'Go to ' . $listF->name->value . ' review',

        'FrontPageAffiliateButtonUrl' => '',
        'FrontPageAffiliateButtonText' => 'Go to ' . $listF->name->value,
        /**/


      ];
    }
    return $listFormat;

  }

  public
  function getStreamList() {
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

  public function getShortcode($text) {
    if (is_string($text)) {
      $shortcodeEngine = \Drupal::service('shortcode');
      return $shortcodeEngine->process($text);
    }
    return $text;
  }

  public
  function getClearUrl($string) {
    $string = trim($string, "\t\n\r\0\x0B");

    //--- Latin ---//
    $string = str_replace('ü', 'u', $string);
    $string = str_replace('Á', 'A', $string);
    $string = str_replace('á', 'a', $string);
    $string = str_replace('é', 'e', $string);
    $string = str_replace('É', 'E', $string);
    $string = str_replace('í', 'i', $string);
    $string = str_replace('Í', 'I', $string);
    $string = str_replace('ó', 'o', $string);
    $string = str_replace('Ó', 'O', $string);
    $string = str_replace('Ú', 'U', $string);
    $string = str_replace('ú', 'u', $string);

    //--- Nordick ---//
    $string = str_replace('ø', 'o', $string);
    $string = str_replace('Ø', 'O', $string);
    $string = str_replace('Æ', 'E', $string);
    $string = str_replace('æ', 'e', $string);
    $string = str_replace('Å', 'A', $string);
    $string = str_replace('å', 'a', $string);
    //--- Others ---//
    $string = str_replace(' - ', '-vs-', $string);
    $string = str_replace(' ', '_', $string);

    $puntationArray = [
      '\"',
      ':',
      ',',
      ';',
      '!',
      '#',
      '¤',
      '%',
      '&',
      '/',
      '(',
      ')',
      '=',
      '?',
      '´',
      '¡',
      '@',
      '£',
      '$',
      '½',
      '¥',
      '{',
      '[',
      ']',
      '}',
      '±',
      '|',
      '*',
    ];
    $string = str_replace($puntationArray, '_', $string);

    $string = strtolower($string);
    $string = trim($string, "\t\n\r\0\x0B");
    return $string;
  }

  public
  function getTree($eventList, $format) {
    $tree['AllEvents'] = [];
    foreach ($eventList as $event) {
      $date = date($format, $event['eventDate']);
      $league = $event['eventTournamentName'];
      $tournament_id = $event['eventTournamentID'];
      $tournamentAPIID = $event['eventTournamentAPIID'];
      $eventWeight = $event['order'];
      $tournamentWeight = $event['tournamentWeight'];
      $url = $event['eventTournamentLogo'];
      if (!(@$tree['AllEvents'][$date])) {
        $tree['AllEvents'][$date] = ['date' => $date];
        $tree['AllEvents'][$date]['alltournament'][$league]['events'] = [];

        $tree['AllEvents'][$date]['alltournament'][$league]['tournament'] = $league;
        $tree['AllEvents'][$date]['alltournament'][$league]['Torder'] = ($eventWeight == -1) ? $eventWeight : $tournamentWeight;
        $tree['AllEvents'][$date]['alltournament'][$league]['eventTournamentIDAcoordion'] = $this->getClearUrl($date . $league . $tournament_id);
        $tree['AllEvents'][$date]['alltournament'][$league]['tournament_id'] = $tournament_id;
        $tree['AllEvents'][$date]['alltournament'][$league]['tournamentIMG'] = $url;
        array_push($tree['AllEvents'][$date]['alltournament'][$league]['events'], $event);
      }
      else {
        if (@$tree['AllEvents'][$date]) {
          if (!(@$tree['AllEvents'][$date]['alltournament'][$league]) and (@$tree['AllEvents'][$date][$league]['tournament_id']) != $tournament_id) {
            $tree['AllEvents'][$date]['alltournament'][$league]['tournament'] = $league;
            $tree['AllEvents'][$date]['alltournament'][$league]['Torder'] = ($eventWeight == -1) ? $eventWeight : $tournamentWeight;
            $tree['AllEvents'][$date]['alltournament'][$league]['eventTournamentIDAcoordion'] = $this->getClearUrl($date . $league . $tournament_id);
            $tree['AllEvents'][$date]['alltournament'][$league]['tournament_id'] = $tournament_id;
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

    return $this->orderTree($tree);
  }

  function orderTree($tree) {
    foreach ($tree as &$allDays) {
      foreach ($allDays as &$Today) {
        $Today['alltournament'] = $this->getOrderByPrioryty($Today['alltournament'], 'Torder');
        foreach ($Today['alltournament'] as &$TodayEventsPerTournament) {
          if (count($TodayEventsPerTournament['events']) >= 2) {
            $TodayEventsPerTournament['events'] = $this->getOrderByPrioryty($TodayEventsPerTournament['events'], 'order');
          }
        }
      }
    }
    return $tree;
  }

  public
  function getOrderByPrioryty($array, $on, $order = SORT_ASC) {
    $new_array = [];
    $sortable_array = [];
    if (count($array) > 0) {
      foreach ($array as $k => $v) {
        if (is_array($v)) {
          foreach ($v as $k2 => $v2) {
            if ($k2 == $on) {
              $sortable_array[$k] = $v2;
            }
          }
        }
        else {
          $sortable_array[$k] = $v;
        }
      }
      switch ($order) {
        case SORT_ASC:
          asort($sortable_array);
          break;
        case SORT_DESC:
          arsort($sortable_array);
          break;
      }

      foreach ($sortable_array as $k => $v) {
        $new_array[$k] = $array[$k];
      }
    }
    return $new_array;
  }

  public function getTournamentList() {
    $data = [];
    $sport = $this->getSport();
    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $tree = $manager->loadTree('sport', $sport['sportDrupalId'], NULL, TRUE);

    foreach ($tree as $term) {
      if (empty($manager->loadChildren($term->id())) && $term->field_no_event_sport->value == 1) {
        $data['Tournaments'][] = [
          'tournamentId' => $term->id(),
          'tournamentName' => $term->name->value,
          'tournamentDescription' => $this->getShortcode($term->field_no_event_sport_description->value),
          'tournamentLink' => $this->getTaxonomyAlias($term->id()),
        ];
      }
    }

    return $data;
  }


  /*------------- Sports ------------*/

  public function getSchedulePerTournament($range, $TournamentID, $days = 3) {
    set_time_limit(120);
    $date = date('Y-m-d');
    $fromDate = strtotime($date);
    $endDate = strtotime('+' . $days . ' day', strtotime($date));
    $channel = $this->getChannelTaxonomyId();
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_event_tournament', $TournamentID)
      ->condition('field_event_date', $fromDate, '>')
      ->condition('field_event_date', $endDate, '<')
      ->condition('field_event_channels', $channel, '=')
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    $all_nodes = $this->getNodes($ids->execute(), TRUE);

    if (empty($all_nodes)) {
      $ids = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('promote', 1)
        ->condition('type', 'events')
        ->condition('field_event_tournament', $TournamentID)
        ->condition('field_event_channels', $channel, '=')
        ->sort('field_event_date', 'ASC')
        ->sort('field_event_tournament', 'ASC');
      if ($range != 0) {
        $ids->range(0, $range);
      }
      $all_nodes = $this->getNodes($ids->execute(), TRUE);
    }
    return $all_nodes;
  }


  /*------------- Schedule ------------*/

  public
  function getPageBackground() {
    $node = $this->getNodeByUrl();
    $sport = $this->getSport();

    if (isset($node['field_' . $node['type'][0]['target_id'] . '_bg']) && $node['field_' . $node['type'][0]['target_id'] . '_bg'] != NULL) {
      return $this->getImgUrl($node['field_' . $node['type'][0]['target_id'] . '_bg'][0]['target_id']);
    }
    elseif (isset($sport['sportBackground']) && $sport['sportBackground'] != NULL) {
      return $sport['sportBackground'];
    }
    return NULL;
  }

  public
  function getPageBackgroundByNodeID($id) {
    $node = Node::Load($id);
    $sport = $this->getSport();
    if ($node) {
      $node = $node->toArray();
      if (isset($node['field_' . $node['type'][0]['target_id'] . '_bg']) && $node['field_' . $node['type'][0]['target_id'] . '_bg'] != NULL) {
        return $this->getImgUrl($node['field_' . $node['type'][0]['target_id'] . '_bg'][0]['target_id']);
      }
    }
    if (isset($sport['sportBackground']) && $sport['sportBackground'] != NULL) {
      return $sport['sportBackground'];
    }

    return NULL;
  }

  public
  function getMenuLinks() {
    $active = $this->getSport();
    $menu_link = \Drupal::entityTypeManager()
      ->getStorage('menu_link_content')
      ->loadByProperties([
        'menu_name' => 'main',
        'weight' => '0',
        'enabled' => TRUE,
      ]);
    $nodes = [];
    $links = [];

    foreach ($menu_link as $sport) {
      $nid = $sport->description->value;
      $nodes[] = $this->getNodeByCriterio([
        'type' => 'sport',
        'field_sport_sport' => $nid,
      ], 0);
    }


    foreach ($nodes as $node) {
      $term_id = $node->field_sport_sport->target_id;
      $colors = [];

      $link = [
        'url' => $this->getNodeAlias($node->id()),
        'title' => $node->title->value,
      ];

      if ($active != NULL && $term_id == $active['sportDrupalId']) {
        $link['active'] = TRUE;
      }

      if ($term_id) {
        $term = $this->getTaxonomyByCriterio([
          'tid' => $term_id,
          'vid' => 'sport',
        ]);
        if ($term->field_base_color->value) {
          $color = $term->field_base_color->value;
          $hsl = $this->hexToHsl(substr($color, 1));
          $link['colors'] = [
            'main' => $color,
            'gradient_dark' => '#' . $this->hslToHex([
                $hsl[0],
                $hsl[1],
                $hsl[2] - 0.1 > 0 ? $hsl[2] - 0.1 : 0,
              ]),
            'gradient_light' => '#' . $this->hslToHex([
                $hsl[0],
                $hsl[1],
                $hsl[2] + 0.1 < 0.99 ? $hsl[2] + 0.1 : 0.99,
              ]),
          ];
          if ($link['active']) {
            $links['active'] = $color;
          }
        }
      }

      $links['links'][] = $link;
    }

    return $links;
  }

  public
  function hexToHsl($hex) {
    $hex = [$hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5]];
    $rgb = array_map(function ($part) {
      return hexdec($part) / 255;
    }, $hex);

    $max = max($rgb);
    $min = min($rgb);

    $l = ($max + $min) / 2;

    if ($max == $min) {
      $h = $s = 0;
    }
    else {
      $diff = $max - $min;
      $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

      switch ($max) {
        case $rgb[0]:
          $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
          break;
        case $rgb[1]:
          $h = ($rgb[2] - $rgb[0]) / $diff + 2;
          break;
        case $rgb[2]:
          $h = ($rgb[0] - $rgb[1]) / $diff + 4;
          break;
      }

      $h /= 6;
    }

    return [$h, $s, $l];
  }

  public
  function hslToHex($hsl) {
    list($h, $s, $l) = $hsl;

    if ($s == 0) {
      $r = $g = $b = 1;
    }
    else {
      $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
      $p = 2 * $l - $q;

      $r = $this->hue2rgb($p, $q, $h + 1 / 3);
      $g = $this->hue2rgb($p, $q, $h);
      $b = $this->hue2rgb($p, $q, $h - 1 / 3);
    }

    return $this->rgb2hex($r) . $this->rgb2hex($g) . $this->rgb2hex($b);
  }

  /*------------------Tools--------------------------*/

  public
  function hue2rgb($p, $q, $t) {
    if ($t < 0) {
      $t += 1;
    }
    if ($t > 1) {
      $t -= 1;
    }
    if ($t < 1 / 6) {
      return $p + ($q - $p) * 6 * $t;
    }
    if ($t < 1 / 2) {
      return $q;
    }
    if ($t < 2 / 3) {
      return $p + ($q - $p) * (2 / 3 - $t) * 6;
    }

    return $p;
  }

  public
  function rgb2hex($rgb) {
    return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
  }

  public
  function getSocialNetworks() {
    $terms = $this->getTaxonomyByCriterio(['vid' => 'social_networks'], 1);
    $networks = [];
    foreach ($terms as $term) {
      $networks[] = [
        'name' => $term->name->value,
        'image' => $this->getImgUrl($term->field_social_networks_image->target_id),
        'link' => $term->field_social_networks_link->uri,
      ];
    }

    return $networks;
  }

  public
  function getLiveStreamReviewsFormat() {
    $streamProviders = $this->getStreamProviders();

    $streams = [];
    foreach ($streamProviders as $provider) {

      $streams[] = [
        'name' => $provider->name->value,
        'streamId' => $provider->id(),
        'logo' => $this->getImgUrl($provider->field_streamprovider_logo->target_id),
        'link1url' => '/live-stream-reviews/' . $provider->id(),
        'link1text' => 'Lees beoordeling',
        'link2url' => 'google.com',
        'link2text' => 'Go to ' . $provider->name->value,
        'rating' => 4,
        'description' => 'Bekend vanwege hun TV kanaal is Eurosport nu ook via het internet te volgen. Ze bieden veel sport, de hele dag door. Geen enkele sport is voor hen te klein.',
      ];
    }
    return $streams;
  }

  public
  function getStreamProviders() {
    $manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $terms = $manager->loadTree('stream_provider', 0, 2, TRUE);

    $streamProviders = [];
    foreach ($terms as $term) {
      if (!empty($manager->loadParents($term->id()))) {
        $streamProviders[] = $term;
      }
    }
    return $streamProviders;
  }

  //Color Functions

  public
  function getStreamProviderTabs() {
    $route = \Drupal::routeMatch();
    $routeName = $route->getRouteName();

    if ($routeName == 'rp_cms_steve_integration.live_stream_review') {
      $streamProviders = $this->getStreamProviders();

      $links = [];
      foreach ($streamProviders as $provider) {
        $links[] = [
          'name' => $provider->name->value,
          'url' => '/live-stream-reviews/' . $provider->id(),
          'active' => $route->getParameter('term') == $provider->id() ? TRUE : FALSE,
        ];
      }

      return $links;
    }
    return NULL;
  }

  public
  function getStreamProviderFormat() {
    $term = $this->getTaxonomyTermByUrl();
    if ($term) {

      $info = [
        'name' => $term->name->value,
        'logo' => $this->getImgUrl($term->field_streamprovider_logo->target_id),
        'url' => 'google.com',

        'ProviderFeatures1' => 'Se Champions League online',
        'ProviderFeatures2' => 'Adgang til en masse serier',
        'ProviderFeatures3' => 'HD-kvalitet',

        'Provider1Heading' => 'Udvalget',
        'Provider1Content' => 'Hos Viaplayfår du lidt af det hele. Du kan se alle deres egne programmer, ligesom de også har et udemærket udvalg af film og serier, og så kan du se sport i stor stil. Som lidt ekstra kan du gennem Viaplay leje nye filmudgivelser, som ikke er at finde i kataloget.',
        'Provider1Stars' => 4,

        'Provider2Heading' => 'Live streaming',
        'Provider2Content' => 'Det er her, at Viaplay udmærker sig for alvor. Viasat har blandt andet rettighederne til at vise Champions League, Formel 1 og ikke mindst NHL og det kan alt sammen streames på nettet. Men hos Viaplay kan du også lade dig underholde af mere nicheprægede sportsgrene som eksempelvis dart, Nascar, atletik og speedway. Der er rigeligt at tage fat på, så det er bare at slå sig løs.',
        'Provider2Stars' => 5,

        'Provider3Heading' => 'Support',
        'Provider3Content' => 'Man kan kontakte Viaplays kundesupport på flere måder. Enten ved at ringe til dem på tlf: 77 331 301 i tidsrummet mellem klokken 9-20 på hverdage og 12-18 i weekenden. Eller du kan skrive til dem på support@viaplay.dk. Der udover er der mulighed for at skrive til dem gennem deres supportforum eller Facebook.',
        'Provider3Content_1' => 'Ved store begivenheder holder de ofte supporten åben uden for normal åbningstid.',
        'Provider3Stars' => 2,

        'Provider4Heading' => 'Brugervenlighed',
        'Provider4Content' => 'Det er nemt at bruge Viaplay. Alt der delt ind i kategorier om lige at finde. Så er det bare at dobbeltklikke på, hvad man gerne vil se, og så er man i gang. Kvaliteten er der heller ikke noget at klage over, det kører. Under kategorien sport er dagens udvalg listet op, lige til at gå til.',
        'Provider4Stars' => 4,

        'Provider5Heading' => 'Vores erfaring',
        'Provider5Content' => 'Vi har ikke haft de store problemer. Live stream kører, det er nemt at bruge. De skifter fint i indholdet, så der altid er noget, man kan underholde sig med. Dog kunne vi godt tænke os, at de havde en livechat. Skal man have svar hurtigt, er det nødvendigt at ringe. De andre muligheder kan tage noget tid før, der kommer respons.',
        'Provider5Stars' => 4,

        'Provider6Heading' => 'Konklusion',
        'Provider6Content' => 'Generelt er vi glade for Viaplay. Det er helt klart på sporten, at de er bedst, men her har de også nogle af de helt tunge rettigheder. Eneste minus er, at man kun kan bruge abonnementet på fem enheder, hvilket er noget, som kan give problemer. Skal man skifte enhed kræver det, at man ringer til kundeservice.',
        'Provider6Stars' => 4,

        'ProviderSponsored' => 'Sponsoreret indhold',
        'gotoButton' => 'Go to ' . $term->name->value,
        'step1' => $this->getImgUrl($term->field_step_1->target_id),
        'step2' => $this->getImgUrl($term->field_step_2->target_id),
        'step3' => $this->getImgUrl($term->field_step_3->target_id),

      ];

      return $info;
    }
    return NULL;
  }

  public
  function getSchedulePager($pagesNumber) {
    $fromDate = strtotime(date('Y-m-d'));
    $sport = $this->getSport();
    $channel = $this->getChannelTaxonomyId();
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_events_sport', $sport['sportDrupalId'])
      ->condition('field_event_date', $fromDate, '<=')
      ->condition('field_event_channels', $channel, '=')
      ->pager($pagesNumber)
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    $all_nodes = $this->getNodes($ids->execute());
    return $this->getScheduleFormat($all_nodes);
  }

  public
  function getStreamByChannel($eventProperties = []) {
    $region = $_SESSION["region"];
    $streamActiveList = [];
    foreach ($eventProperties->$region->streamers as $steam) {
      foreach ($steam->meta as $metas) {
        if ($metas->channel == $_SESSION["channel"]) {
          $obj = [
            'vid' => 'stream_provider',
            'field_api_id' => $steam->id,
          ];
          $term = $this->getTaxonomyByCriterio($obj);
          if ($term) {
            $streamActiveList[] = [
              'target_id' => $term->id(),
            ];
          }
        }
      }
    }
    $streamList = $this->getStreamEventList($streamActiveList);
    return $streamList;
  }

  public
  function getScheduleLiveStreamEvents($range) {
    $fromDate = strtotime(date("Y-m-d H:i:s"));
    $sport = $this->getSport();
    $channel = $this->getChannelTaxonomyId();


    $query = \Drupal::entityQuery('node');

    $group = $query->orConditionGroup()
      ->condition('field_event_promotetolivestream', $channel, '=')
      ->condition('field_event_promotetolivestream', $sport['sportDrupalId'], '=');

    $ids = $query
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'events')
      ->condition('field_events_sport', $sport['sportDrupalId'])
      ->condition('field_event_date', $fromDate, '>=')
      ->condition('field_event_channels', $channel, '=')
      ->condition($group)
      ->sort('field_event_date', 'ASC')
      ->sort('field_event_tournament', 'ASC');
    if ($range != 0) {
      $ids->range(0, $range);
    }

    $all_nodes = $this->getNodes($ids->execute());
    return $this->getScheduleFormat($all_nodes);
  }

  public
  function orderMultiDimensionalArray($toOrderArray, $field, $inverse = FALSE) {
    $position = [];
    $newRow = [];
    foreach ($toOrderArray as $key => $row) {
      $position[$key] = $row[$field];
      $newRow[$key] = $row;
    }
    if ($inverse) {
      arsort($position);
    }
    else {
      asort($position);
    }
    $returnArray = [];
    foreach ($position as $key => $pos) {
      $returnArray[] = $newRow[$key];
    }


    return $returnArray;
  }

  public
  function getPHPVarDump($data, $type = 0) {
    echo '<div class="col-sm-offset-3 col-sm-6 alert alert-info"> <pre style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;>';
    if ($type == 0) {
      var_dump($data);
      echo "</pre></div>";
    }
    if ($type == 1) {
      var_dump(count($data));
      echo "</pre></div>";
      exit();
    }
    if ($type == 2) {
      var_dump($data);
      echo "</pre></div>";
      exit();
    }
    else {
      var_dump($data);
    }

  }

  public function getColors() {
    $sportObj = $this->getSport();
    $color = $sportObj['sportColor'];
    if ($color) {
      $hsl = $this->hexToHsl(substr($color, 1));
      $colors = [
        'main' => $color,
        'button_gradient_dark' => '#' . $this->hslToHex([
            $hsl[0],
            $hsl[1],
            $hsl[2] - 0.1 > 0 ? $hsl[2] - 0.1 : 0,
          ]),
        'button_gradient_light' => '#' . $this->hslToHex([
            $hsl[0],
            $hsl[1],
            $hsl[2] + 0.1 < 0.99 ? $hsl[2] + 0.1 : 0.99,
          ]),
        'main_light' => '#' . $this->hslToHex([$hsl[0], $hsl[1], 0.90]),
        'tab_gradient_1' => '#' . $this->hslToHex([$hsl[0], 0.90, 0.99]),
        'tab_gradient_2' => '#' . $this->hslToHex([$hsl[0], 0.50, 0.75]),
        'tab_gradient_3' => '#' . $this->hslToHex([$hsl[0], 0.55, 0.75]),
        'arrow_1' => $color,
        'arrow_2' => $color,
        'schedule_background' => '#' . $this->hslToHex([$hsl[0], 0.22, 0.86]),
        'schedule_background_2' => '#' . $this->hslToHex([$hsl[0], 0.45, 0.60]),
      ];
      return $colors;
    }
    return $color;
  }

  public function getLanguageList() {
    $taxonomy = $this->getTaxonomyByCriterio(['vid' => 'others_sites'], 1);

    $list = [];
    foreach ($taxonomy as $taxonomy) {
      $img = @$this->getImgUrl($taxonomy->field_flag_image->target_id);

      $active = FALSE;
      if ($taxonomy->field_active->value == '1') {
        $active = TRUE;
      }

      $list[] = [
        'name' => $taxonomy->name->value,
        'flag' => (isset($img)) ? file_create_url($img) : '',
        'link' => $taxonomy->field_link->uri,
        'active' => $active,
      ];
    }
    return $list;
  }

  public function getAutor($nid) {
    $node = Node::load($nid)->toArray();
    $uid = $node['uid'][0]['target_id'];
    $user = User::load($uid)->toArray();
    return $user['name'][0]['value'];
  }

  function getImageUrlPlusDefault($idImage) {
    if (isset($idImage) and $idImage != NULL and $idImage != '') {
      $img = File::load($idImage)->toArray();
      $imgUrl = $img["uri"][0]["value"];
      $imgUrl = file_create_url($imgUrl);
      return $imgUrl;
    }
    else {
      return $_SERVER['HTTP_HOST'] . "/themes/custom/stevethemebase/src/images/gig.png";
    }
  }
}
