<?php

namespace Drupal\sesport_blocks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
/**
 * Class getCMSdata.
 */
class getCMSdata extends ControllerBase {

  /**
   * Getcmsdata.
   *
   * @return string
   *   Return Hello string.
   */
  public function getCMSdata() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: getCMSdata')
    ];
  }

  public function getSiteByID($siteID)
  {
    $site = \Drupal::entityTypeManager()
      ->getStorage('site')
      ->loadByProperties(['field_site_api_id' => $siteID]);
    return $site;
  }

  public function getTaxonomy($name)
  {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $name]);
    return $taxonomy;
  }

  public function getTaxonomyByID($id)
  {
    $taxonomy = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['tid' => $id]);
    return reset($taxonomy);
  }

  public function getTaxonomyByAPIID($id)
  {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['field_api_id' => $id]);
    return reset($taxonomy);
  }

  public function getNode($name, $type, $opc){
    $id_node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['type' => $type, $opc => $name]);
    return $id_node;
  }

  public function getIdChannelByNode($nodList)
  {
    $tags_array = [];
    foreach ($nodList as $id) {
      $ischanel = self::getNode($id, 'channels', 'field_channel_api_id');
      $term = reset($ischanel);
      $tags_array [] = ['target_id' => $term->id()];
    }
    return $tags_array;
  }

  public function getTaxonomyParent($competition) {
    $rpClient = RPAPIClient::getClient();
    $index = count($competition) - 1;
    $tournamentParent = $competition[$index][0]["parent"];
    $parameters = ['id' => $tournamentParent];
    if ($tournamentParent != null) {
      $newCompetition = $rpClient->getCompetitionsbyID($parameters);
      $competition[count($competition)] = $newCompetition;

    } else {
      $newCompetition = $rpClient->getCompetitionsbyID($parameters);
      $competition[count($competition)] = $newCompetition;
    }
    return $competition;
  }

  public function getClearUrl($s)
  {
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
    return $s;
  }

  public function getSchedulePlusTree($range = 0, $sport_name = 'Fodbold', $format = "Y-m-d") {
    $nodes = $this->getSchedule($range, $sport_name);
    $tree = $this->getTree($nodes, $format);
    return $tree;

  }

  public function getSchedule($range, $sport_name) {
    $fromDate = strtotime(date('Y-m-d'));
    $sport = $this->getTaxonomyByAPIID($sport_name);
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'game_pages');
    $query->condition('field_tags', $sport->id());
    $query->condition('field_game_date',$fromDate, '>');
    $query->sort('field_game_date', 'ASC');
    $query->sort('field_game_tournament_api_id', 'ASC');
    $query->range(0, $range);
    $ids = $query->execute();
    $all_nodes = $this->getNodes($ids);
    return $all_nodes;
  }

  public function getScheduleTreebyDate($sport_name = 'Fodbold', $format = "Y-m-d") {
    $startDate = date('Y-m-d 00:00:00');
    $tempDate = strtotime('+' . 3 . ' day',strtotime($startDate));
    $endDate = date('Y-m-d 23:59:59', $tempDate);
    $date = ['startdate' => $startDate,'enddate' => $endDate];
    $nodes = $this->getSchedulebyDate($date, $sport_name);
    $tree = $this->getTree($nodes, $format);
    return $tree;

  }

  public function getSchedulebyDate ($date,$sport_name = 'Fodbold') {
    $startdate = strtotime($date['startdate']);
    $enddate = strtotime($date['enddate']);
    $sport = $this->getTaxonomyByAPIID($sport_name['sportApiId']);
    //$sport = reset($sport);
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'game_pages');
    $query->condition('field_tags', $sport->id());
    $query->condition('field_game_date',$startdate, '>');
    $query->condition('field_game_date',$enddate, '<');
    $query->sort('field_game_date', 'ASC');
    $query->sort('field_game_tournament_api_id', 'ASC');
    //$query->range(0,100);
    $ids = $query->execute();
    $all_nodes = $this->getNodesTree($ids);
    return $all_nodes;
  }

  public function getNodesTree($ids) {
    $all_nodes = [];
    foreach ($ids as $id) {
      $node = Node::load($id);
      $node = $node->toArray();
      $url = Url::fromRoute('entity.node.canonical', ['node' => $node["nid"][0]["value"]])->toString();
      $tournamentTaxonomy = $node["field_game_tournament_reff"][0]["target_id"];
      $tournament = $this->getNode($tournamentTaxonomy,'tournament_page','field_tournament_reff');
      $tournament = reset($tournament);
      $tournamentname = $tournament->gettitle();
      $tournamentid = $tournament->id();
      $tournamentApiId = $tournament->field_tournament_api_id->value;
      $eventStream = $node["field_stream_provider_gp"];
      $streamProviderArray = [];
      foreach ($eventStream as $stream)
      {
        $streamProvider = $this->getNode($stream,'stream_provider','field_stream_provider');
        $streamProvider = reset($streamProvider);
        $streamProviderArray[]=$streamProvider;
      }
      $eventStreamProvider = $this->getEventStreamsProvider($streamProviderArray);
      $nodeFormated=[
        'eventName'=>  $node["title"][0]["value"],
        'eventDate'=>  $node["field_game_date"][0]["value"],
        'eventAlias'=> $url ,
        'eventTournamentName'=> $tournamentname ,
        'eventTournamentID'=> $tournamentid,
        'eventTournamentAPIID'=> $tournamentApiId,
        'eventStreams'=> $eventStreamProvider
      ];
      $all_nodes [] = $nodeFormated;
    }
    return $all_nodes;
  }

  public function getTree($data, $format) {
    $tree['AllEvents'] = [];
    foreach ($data as $event) {
      $date = date($format, $event['eventDate']);
      $league = $event['eventTournamentName'];
      $tournament_id = $event['eventTournamentID'];
      $tournamentAPIID = $event['eventTournamentAPIID'];
      $tournamentPage = $this->getNode($tournamentAPIID,'tournament_page','field_tournament_api_id');
      $tournamentPage = reset($tournamentPage);
      $id_1= $tournamentPage->field_logo->target_id;
      $img_1 = @File::load($id_1)->toArray();
      $url = $img_1["uri"][0]["value"];


      if (!(@$tree['AllEvents'][$date])) {
                    $tree['AllEvents'][$date] = ['date'=>$date];
                    $tree['AllEvents'][$date]['alltournament'][$league]['events'] = [];
                    $tree['AllEvents'][$date]['alltournament'][$league]['tournament'] = $league;
                    $tree['AllEvents'][$date]['alltournament'][$league]['tournament_id'] = $tournament_id;
                    $tree['AllEvents'][$date]['alltournament'][$league]['tournamentIMG'] = $url;
                    $tree['AllEvents'][$date]['alltournament'][$league]['eventTournamentIDAcoordion'] = $this->getClearUrl($date.$tournament_id);
         array_push($tree['AllEvents'][$date]['alltournament'][$league]['events'], $event);
      }
      else {
        if (@$tree['AllEvents'][$date]) {
          if (!(@$tree['AllEvents'][$date]['alltournament'][$league]) and (@$tree['AllEvents'][$date][$league]['tournament_id']) != $tournament_id) {
                        $tree['AllEvents'][$date]['alltournament'][$league]['tournament'] = $league;
                        $tree['AllEvents'][$date]['alltournament'][$league]['tournament_id'] = $tournament_id;
                        $tree['AllEvents'][$date]['alltournament'][$league]['eventTournamentIDAcoordion'] = $this->getClearUrl($date.$tournament_id);
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

  public function getStream() {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'stream_provider');
    $query->sort('field_properties_rating', 'ASC');
    $ids = $query->execute();
    $all_nodes = $this->getNodes($ids);
    // return $all_nodes;
    return [
      '#type' => 'markup',
      '#markup' => $all_nodes,
    ];
  }

  public function getNodes($ids) {
    $all_nodes = [];
    foreach ($ids as $id) {
      $node = Node::load($id);
      $all_nodes [] = $node->toArray();
    }
    return $all_nodes;
  }

  public function getSport( ) {
    $node = \Drupal::routeMatch()->getParameter('node');
    $node = $node->toArray();
    $type = $node["type"][0]["target_id"];
    $sportName = [];

    if($type == 'sport_pages'){
      $sportApiid = $node["field_sport_api_id"][0]["value"];
      $sport_Name = $this->getClearUrl($node["field_sport_name"][0]["value"]);
      $sportName = [
        'sportName'=> $sport_Name,
        'sportApiId'=> $sportApiid
      ];

    }
    else if($type == 'sport_internal_pages') {
      $sportApiid = $node["field_sportip_api_id"][0]["value"];
      $sportNameTaxonomya = $this->getTaxonomyByAPIID($sportApiid)->toArray();
      $sport_Name = $this->getClearUrl($sportNameTaxonomya["name"][0]["value"]);
      $sportName = [
        'sportName'=> $sport_Name,
        'sportApiId'=> $sportApiid
      ];

    }
    else if($type == 'sport_internal_blogs') {
      $sportApiid = $node["field_sport_blog_ip_api_id"][0]["value"];
      $sportNameTaxonomya = $this->getTaxonomyByAPIID($sportApiid)->toArray();
      $sport_Name = $this->getClearUrl($sportNameTaxonomya["name"][0]["value"]);
      $sportName = [
        'sportName'=> $sport_Name,
        'sportApiId'=> $sportApiid
      ];

    }
    else if($type == 'game_pages') {
      $sportApiid = $node["field_tags"][0]["target_id"];
      $sportNameTaxonomy = $this->getTaxonomyByID($sportApiid)->toArray();
      $sport_Name = $this->getClearUrl($sportNameTaxonomy["name"][0]["value"]);
      $sportName = [
        'sportName'=> $sport_Name,
        'sportApiId'=> $sportNameTaxonomy["field_sport_api_id"][0]["value"]
      ];

    }

    return $sportName;

  }

/*----------------------------------------------------------------------------*/

  public function getStreamsProvider() {
    $all_nodes = "";
    $nids = \Drupal::entityQuery('node')->condition('type', 'stream_provider')->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      $url = '';
      $id_1 = $node->field_aff_images->target_id;
      if(isset($id_1) and $id_1 != null and $id_1 != ''){
        $img_1 = File::load($id_1)->toArray();
        $url = $img_1["uri"][0]["value"];
      }
      $node = $node->toArray();
      $stream = [
        // --- From General ---
        'idTabsTemplate'=> $this->getClearUrl($node["vid"][0]["value"].'steram'),
        'streamName' => $node["title"][0]["value"],
        'streamIMG' =>$url ,
        'streamVideoQuality' => @$node["field_properties_video_quality"][0]["value"],
        'streamRating' => intval( @$node["field_properties_rating"][0]["value"]),
        'streamPrice' => @$node["field_properties_price"][0]["value"],
        'streamVideoSize' => @$node["field_properties_video_size"][0]["value"],
        // --- From Front Page ---
        //Content
        'FrontPageHeading' => @$node["field_front_page_heading"][0]["value"],
        'FrontPageQualityHeading' => @$node["field_front_page_quality_heading"][0]["value"],
        'FrontPageQualityContent' => @$node["field_front_page_quality_content"][0]["value"],
        'FrontPageUdvalgHeading' => @$node["field_front_page_udvalg_heading"][0]["value"],
        'FrontPageUdvalgContent' => @$node["field_front_page_udvalg_content"][0]["value"],
        'FrontPagePriceHeading' => @$node["field_front_page_price_heading"][0]["value"],
        'FrontPagePriceContent' => @$node["field_front_page_price_content"][0]["value"],
        // Icons
        'FrontPageIconsHeading' => @$node["field_front_page_heading"][0]["value"],
        'FrontPageIcons1Heading' => @$node["field_front_page_icons_1_heading"][0]["value"],
        'FrontPageIcons1Subheading'=> @$node["field_front_page_icons_1_subhead"][0]["value"],
        'FrontPageIcons2Heading'=> @$node["field_front_page_icons_2_heading"][0]["value"],
        'FrontPageIcons3Heading'=> @$node["field_front_page_icons_3_heading"][0]["value"],
        'FrontPageIcons2Subheading'=> @$node["field_front_page_icons_2_subhead"][0]["value"],
        'FrontPageIcons3Subheading'=> @$node["field_front_page_icons_subheadin"][0]["value"],
        'FrontPageIcons3note'=> @$node["field_front_page_icons_3_note"][0]["value"],

        'FrontPageButtonsReviewButton'=> @$node["field_buttons_review"][0]["value"],
        'FrontPageAffiliateButtonUrl'=> @$node["field_front_page_afiliate_button"][0]['uri'],
        'FrontPageAffiliateButtonText'=> @$node["field_front_page_afiliate_button"][0]['title'],
        'DisclaimerContentLiveStreamPage'=> @$node["field_stream_provider_detai"][0]["value"],
        'DisclaimerContentGamePage'=> @$node["field_page_detail_conten"][0]["value"],
        'ProviderPageDetailButtonAffiliateLink'=> @$node["field_ppc_cs_button_aff"][0]["value"],
        'ProviderPageDetailButtonHeading'=> @$node["field_ppc_cs_button_h"][0]["value"],
        'ProviderPageDetailButtonSubheading'=> @$node["field_ppc_cs_button_sub"][0]["value"],
        'ProviderPageDetailContentDisclaimer'=> @$node["field_page_detail_conten"][0]["value"],
        'ProviderPageDetailContentIcon1Heading'=> @$node["field_ppc_cs_i_one"][0]["value"],
        'ProviderPageDetailContentIcon1Subheading'=> @$node["field_ppc_cs_i_one_sub"][0]["value"],
        'ProviderPageDetailContentIcon2Heading'=> @$node["field_ppc_cs_i_two"][0]["value"],
        'ProviderPageDetailContentIcon2Subheading'=> @$node["field_ppc_cs_i_two_sub"][0]["value"],
        'ProviderPageDetailContentIcon3Heading'=> @$node["field_ppc_cs_i_three"][0]["value"],
        'ProviderPageDetailContentIcon3Subheading'=> @$node["field_ppc_cs_i_three_sub"][0]["value"],
        'ProviderPageDetailContentNote'=> @$node["field_providerpage_detail_conten"][0]["value"],
        'ProviderPageDetailHeading'=> @$node["field_providerpage_detail_headin"][0]["value"],
        'ProviderPageDetailPriceHeading'=> @$node["field_ppc_ps_h"][0]["value"],
        'ProviderPageDetailQualityContent'=> @$node["field_providerpage_detail_qualit"][0]["value"],
        'ProviderPageDetailQualityHeading'=> @$node["field_ppc_qs_h"][0]["value"],
        'ProviderPageDetailUdvalgContent'=> @$node["field_udvalg_content"][0]["value"],
        'ProviderPageDetailUdvalgHeading'=> @$node["field_providerpage_detail_udvalg"][0]["value"],

        // Reviw Page

        'ReviewProvidersContentBonus'=> @$node["field_content_bonus"][0]["value"],
        'ReviewProvidersEnable'=> @$node["field_review_providers_enable"][0]["value"],
        ];
      $all_nodes[] = $stream;
    }
    return $all_nodes;

  }

  public function getEventStreamsProvider($nodes) {

    foreach ($nodes as $node) {
      $url = '';
      $id_1 = $node->field_aff_images->target_id;
      if(isset($id_1) and $id_1 != null and $id_1 != ''){
        $img_1 = File::load($id_1)->toArray();
        $url = $img_1["uri"][0]["value"];
      }
      $node = $node->toArray();
      $stream = [

        // --- From General ---
        'idTabsTemplate'=> $this->getClearUrl($node["vid"][0]["value"].'steram'),
        'streamName' => $node["title"][0]["value"],
        'streamIMG' =>$url ,
        'streamVideoQuality' => @$node["field_properties_video_quality"][0]["value"],
        'streamRating' => intval( @$node["field_properties_rating"][0]["value"]),
        'streamPrice' => @$node["field_properties_price"][0]["value"],
        'streamVideoSize' => @$node["field_properties_video_size"][0]["value"],
        // --- From Front Page ---
        //Content
        'FrontPageHeading' => @$node["field_front_page_heading"][0]["value"],
        'FrontPageQualityHeading' => @$node["field_front_page_quality_heading"][0]["value"],
        'FrontPageQualityContent' => @$node["field_front_page_quality_content"][0]["value"],
        'FrontPageUdvalgHeading' => @$node["field_front_page_udvalg_heading"][0]["value"],
        'FrontPageUdvalgContent' => @$node["field_front_page_udvalg_content"][0]["value"],
        'FrontPagePriceHeading' => @$node["field_front_page_price_heading"][0]["value"],
        'FrontPagePriceContent' => @$node["field_front_page_price_content"][0]["value"],
        // Icons
        'FrontPageIconsHeading' => @$node["field_front_page_heading"][0]["value"],
        'FrontPageIcons1Heading' => @$node["field_front_page_icons_1_heading"][0]["value"],
        'FrontPageIcons1Subheading'=> @$node["field_front_page_icons_1_subhead"][0]["value"],
        'FrontPageIcons2Heading'=> @$node["field_front_page_icons_2_heading"][0]["value"],
        'FrontPageIcons3Heading'=> @$node["field_front_page_icons_3_heading"][0]["value"],
        'FrontPageIcons2Subheading'=> @$node["field_front_page_icons_2_subhead"][0]["value"],
        'FrontPageIcons3Subheading'=> @$node["field_front_page_icons_subheadin"][0]["value"],
        'FrontPageIcons3note'=> @$node["field_front_page_icons_3_note"][0]["value"],

        'FrontPageButtonsReviewButton'=> @$node["field_buttons_review"][0]["value"],
        'FrontPageAffiliateButtonUrl'=> @$node["field_front_page_afiliate_button"][0]['uri'],
        'FrontPageAffiliateButtonText'=> @$node["field_front_page_afiliate_button"][0]['title'],
        'DisclaimerContentLiveStreamPage'=> @$node["field_stream_provider_detai"][0]["value"],
        'DisclaimerContentGamePage'=> @$node["field_page_detail_conten"][0]["value"],

        //Game Page

        'ProviderPageDetailButtonAffiliateLink'=> @$node["field_ppc_cs_button_aff"][0]["value"],
        'ProviderPageDetailButtonHeading'=> @$node["field_ppc_cs_button_h"][0]["value"],
        'ProviderPageDetailButtonSubheading'=> @$node["field_ppc_cs_button_sub"][0]["value"],
        'ProviderPageDetailContentDisclaimer'=> @$node["field_page_detail_conten"][0]["value"],
        'ProviderPageDetailContentIcon1Heading'=> @$node["field_ppc_cs_i_one"][0]["value"],
        'ProviderPageDetailContentIcon1Subheading'=> @$node["field_ppc_cs_i_one_sub"][0]["value"],
        'ProviderPageDetailContentIcon2Heading'=> @$node["field_ppc_cs_i_two"][0]["value"],
        'ProviderPageDetailContentIcon2Subheading'=> @$node["field_ppc_cs_i_two_sub"][0]["value"],
        'ProviderPageDetailContentIcon3Heading'=> @$node["field_ppc_cs_i_three"][0]["value"],
        'ProviderPageDetailContentIcon3Subheading'=> @$node["field_ppc_cs_i_three_sub"][0]["value"],
        'ProviderPageDetailContentNote'=> @$node["field_providerpage_detail_conten"][0]["value"],
        'ProviderPageDetailHeading'=> @$node["field_providerpage_detail_headin"][0]["value"],
        'ProviderPageDetailPriceHeading'=> @$node["field_ppc_ps_h"][0]["value"],
        'ProviderPageDetailQualityContent'=> @$node["field_providerpage_detail_qualit"][0]["value"],
        'ProviderPageDetailQualityHeading'=> @$node["field_ppc_qs_h"][0]["value"],
        'ProviderPageDetailUdvalgContent'=> @$node["field_udvalg_content"][0]["value"],
        'ProviderPageDetailUdvalgHeading'=> @$node["field_providerpage_detail_udvalg"][0]["value"],


        'ReviewProvidersContentBonus'=> @$node["field_content_bonus"][0]["value"],
        'ReviewProvidersEnable'=> @$node["field_review_providers_enable"][0]["value"],








      ];
      $all_nodes[] = $stream;
    }
    return $all_nodes;
  }


  public function getTopEvents( ) {
    $sportName =  $this->getSport();
    $nodes = $this->getSchedule(3,$sportName);
    $eventArray=[];
    foreach ($nodes as $node){
      $participantArray=[];
      $url = Url::fromRoute('entity.node.canonical', ['node' => $node["nid"][0]["value"]])->toString();
      $eventArray['events'][]=[
        'eventName'=>  $node["title"][0]["value"],
        'eventDate'=>  $node["field_game_date"][0]["value"],
        'eventAlias'=> $url ,
      ];
      foreach ($node["field_game_participants_tax"] as $eventParticipant){
        $pageParticipant = $this->getNode($eventParticipant["target_id"],'team_content','field_team_tax');
        $pageParticipant = reset($pageParticipant);
        $id_1= $pageParticipant->field_participant_logo->target_id;
        $img_1 = File::load($id_1)->toArray();
        $participantIdLogo = $img_1["uri"][0]["value"];
        $participantArray['participant'][]=[
          'participantName'=>$pageParticipant->gettitle(),
          'participantIdPage'=>$pageParticipant->id(),
          'participantIdLogo'=>$participantIdLogo
        ];
      }
      $eventArray['events'][count($eventArray['events'])-1]['eventParticipant'] = $participantArray;
    }
    $data = [
      ['sportName' => $sportName],
      ['Events'=>$eventArray]
    ];
    return $data;

  }

  public function getLiveStreamTopEvents( ) {
    $sportName =  $this->getSport();
    $nodes = $this->getSchedule(3,$sportName);
    $eventArray=[];
    foreach ($nodes as $node){
      $participantArray=[];
      $url = Url::fromRoute('entity.node.canonical', ['node' => $node["nid"][0]["value"]])->toString();
      $eventArray['events'][]=[
        'eventName'=>  $node["title"][0]["value"],
        'eventDate'=>  $node["field_game_date"][0]["value"],
        'eventAlias'=> $url ,
      ];
      foreach ($node["field_game_participants_tax"] as $eventParticipant){
        $pageParticipant = $this->getNode($eventParticipant["target_id"],'team_content','field_team_tax');
        $pageParticipant = reset($pageParticipant);

        $id_1= $pageParticipant->field_participant_logo->target_id;
        $img_1 = File::load($id_1)->toArray();
        $participantIdLogo = $img_1["uri"][0]["value"];


        $participantArray['participant'][]=[
          'participantName'=>$pageParticipant->gettitle(),
          'participantIdPage'=>$pageParticipant->id(),
          'participantIdLogo'=>$participantIdLogo
        ];
      }
      $eventArray['events'][count($eventArray['events'])-1]['eventParticipant'] = $participantArray;
    }
    $data = [
      ['sportName' => $sportName],
      ['Events'=>$eventArray]
    ];
    return $data;

  }

  public function getGamePage(){
    $nodes = \Drupal::routeMatch()->getParameter('node');
    $node = $nodes->toArray();
    $participantArray=[];
    $eventArray['events'][]=[
        'eventName'=>  $node["title"][0]["value"],
        'eventbody'=>  @$node["body"][0]["value"],
        'eventDate'=>  $node["field_game_date"][0]["value"]
      ];
      foreach ($node["field_game_participants_tax"] as $eventParticipant){
        $pageParticipant = $this->getNode($eventParticipant["target_id"],'team_content','field_team_tax');
        $pageParticipant = reset($pageParticipant);
        $id_1= $pageParticipant->field_participant_logo->target_id;
        $img_1 = File::load($id_1)->toArray();
        $participantIdLogo = $img_1["uri"][0]["value"];
        $participantArray[]=[
          'participantName'=>$pageParticipant->gettitle(),
          'participantIdPage'=>$pageParticipant->id(),
          'participantIdLogo'=>$participantIdLogo
        ];
      }
      $eventArray['events']['Participants'] = $participantArray;
      $eventStream = $node["field_stream_provider_gp"];
      $streamProviderArray = [];
      foreach ($eventStream as $stream)
      {
        $id = $stream["target_id"];
        $streamProvider = $this->getNode($id,'stream_provider','field_stream_provider');
        $streamProvider = reset($streamProvider);
        $streamProviderArray[]=$streamProvider;
      }

      $streamProviderArray = $this->getEventStreamsProvider($streamProviderArray);


      $eventArray['events']['Streamers'] = $streamProviderArray;
      return $eventArray;
  }

}
