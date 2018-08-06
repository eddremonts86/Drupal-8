<?php

namespace Drupal\rp_client_base\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;

/**
 * Class SteveFrontendControler.
 */
class SteveFrontendControler extends SteveBaseControler
{
  public function liveStreamReviewsPage()
  {
    return array(
      '#theme' => 'livestreamreviewspage',
    );
  }

  public function liveStreamReviewPage()
  {
    return array(
      '#theme' => 'livestreamreviewpage',
    );
  }

  public function getPartners($isPromote)
  {
    $data = [
      'vid' => 'stream_provider',
      'field_stream_provider_home_promo' => $isPromote,
    ];
    $partnersList = $this->getTaxonomyByCriterio($data, 1);
    $partnersListFormat = [];
    foreach ($partnersList as $partner) {
      $logoid = @$partner->field_streamprovider_logo->target_id;
      $logo = $this->getImgUrl($logoid);
      $url = $this->getTaxonomyAlias($partner->id());
      $partnersListFormat[] = [
        'name' => $partner->name->value,
        'url' => $url,
        'logo' => $logo,
      ];
    }
    return $partnersListFormat;
  }

  public function getBlogInfo($pagesNumber)
  {
    return $getBlogSchedule = $this->getSchedulePager($pagesNumber);
  }

  public function getEventFullPage()
  {
    $node = $this->getNodeByUrl();
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => $this->getShortcode($node["body"][0]["value"]),
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Participants' => $this->getParticipant($node["field_event_participants"]),
      'Streamers' => $this->getStreamEventList($node["field_event_stream_provider"]),
    );
    return $eventArray;
  }

  public function getEventParticipants()
  {
    $node = $this->getNodeByUrl();
    $participant = $this->getParticipant($node["field_event_participants"], $node["field_events_properties"]);
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => $this->getShortcode($node["body"][0]["value"]),
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Participants' => $participant
    );
    return $eventArray;
  }

  public function getEventHeadByTaxonomy()
  {
    $term = $this->getTaxonomyTermByUrl();
    $events = $this->getFormatedEventList(0, 1);
    if($events){
      $head = $this->getEventHead($events[0]['eventiId']);
      if($head){
        $head['events']['eventName'] = $term->name->value;
      }
      return $head;
    }

    return NULL;
  }

  public function getEventListByTaxonomy($elements = 0, $range = 0, $days = 7)
  {

    $terms = [];
    $fromDate = strtotime(date('Y-m-d'));
    $endDate = $fromDate + 86400 * $days;
    $term = $this->getTaxonomyTermByUrl();
    $taxonomyVid = $term->getVocabularyId();

    if($taxonomyVid == 'participant'){
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('promote', 1)
        ->condition('type', 'events')
        ->condition('field_event_participants', $term->id())
        ->condition('field_event_date', $fromDate, '>=')
        ->condition('field_event_date', $endDate, '<=');
      if($range!=0){ $query->range(0, 15);}
      if($query->execute()){
        $query->sort('field_event_date', 'ASC')
          ->sort('field_event_tournament', 'ASC');
      }else{
        $query = \Drupal::entityQuery('node')
          ->condition('status', 1)
          ->condition('promote', 1)
          ->condition('type', 'events')
          ->condition('field_event_participants', $term->id())
          ->condition('field_event_date', $fromDate, '<=')
          ->sort('field_event_date', 'DESC')
          ->sort('field_event_tournament', 'ASC')
          ->range(0, 15);
      }

    }
    else if($taxonomyVid == 'sport'){
      $terms[] = $term->id();
      $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('sport', $term->id());

      foreach ($tree as $treeElement) {
        if($treeElement->tid){
          $terms[] = $treeElement->tid;
        }
      }

      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('promote', 1)
        ->condition('type', 'events')
        ->condition('field_event_date', $fromDate, '>=')
        ->condition('field_event_date', $endDate, '<=')
        ->condition('field_event_tournament', $terms, 'IN');
      if($query->execute()){
        $query->sort('field_event_date', 'ASC')
          ->sort('field_event_tournament', 'ASC');
      }else{
        $query = \Drupal::entityQuery('node')
          ->condition('status', 1)
          ->condition('promote', 1)
          ->condition('type', 'events')
          ->condition('field_event_date', $fromDate, '<=')
          ->condition('field_event_tournament', $terms, 'IN')
          ->sort('field_event_date', 'DESC')
          ->sort('field_event_tournament', 'ASC')
          ->range(0, 15);
      }

    }
    else{ return NULL;}
    if($elements){ $query->pager($elements);}
    $events = $this->getNodes($query->execute());

    return $events;
  }

  public function getFormatedEventList($elements = 0, $range = 0, $days = 7){
    $data = [];
    $events = $this->getEventListByTaxonomy($elements, $range, $days);

    foreach ($events as $event) {
      $data[] = [
        'eventName' => $event['title'][0]['value'],
        'eventLink' =>  $this->getNodeAlias($event['nid'][0]['value']),
        'eventiId' => $event['nid'][0]['value'],
        'eventDate' =>  $event['field_event_date'][0]['value']
      ];
    }

    return $data;
  }

  public function getEventListSchedule($elements = 0, $range = 0, $days = 7){
    $events = $this->getEventListByTaxonomy($elements, $range, $days);
    $nodes = $this->getScheduleFormat($events);
    $tree = $this->getTree($nodes, "Y-m-d");
    return $tree;
  }

  public function getEventBodyByTaxonomy()
  {
    $data = [];
    $term = $this->getTaxonomyTermByUrl();

    $data['Description'] = $this->getShortcode($term->description->value);

    return $data;
  }

  public function getEventBackgroundByTaxonomy()
  {
    $taxonomyBG = false;
    $taxonomy = $this->getTaxonomyTermByUrl();
    $vocabolary = $taxonomy->getVocabularyId();
    if($vocabolary== "participant"){
      $taxonomyIMGID = $taxonomy->field_participant_background_pag->target_id;
      $taxonomyBG = $this->getImgUrl($taxonomyIMGID);
      return $taxonomyBG;
    }
    else if ($vocabolary == "sport"){
      $taxonomyIMGID = $taxonomy->field_background->target_id;
      $taxonomyBG = $this->getImgUrl($taxonomyIMGID);
      return $taxonomyBG;
    }
    if(!$taxonomyBG){
      $events = $this->getFormatedEventList(0,1);
      return $this->getPageBackgroundByNodeID($events[0]['eventiId']);
    }
  }

  public function getEventParticipantsByNodeId($id)
  {
    $node = Node::load($id)->toArray();
    $participant = $this->getParticipant($node["field_event_participants"], $node["field_events_properties"]);
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => $this->getShortcode($node["body"][0]["value"]),
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Participants' => $participant
    );
    return $eventArray;
  }

  public function getEventStream($id = NULL)
  {
	if($id){
		$node = Node::load($id)->toArray(); 
	}else{ 
		$node = $this->getNodeByUrl();
    }
    
    if (isset($node["field_events_properties"][0]["value"])) {
      $eventProperties = $data = \GuzzleHttp\json_decode($node["field_events_properties"][0]["value"]);
    }
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => $this->getShortcode($node["body"][0]["value"]),
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Streamers' => $this->getStreamByChannel($eventProperties),
      'sport' => $this->getSport(),
    );
    return $eventArray;
  }

  public function getEventcontent()
  {
    $node = $this->getNodeByUrl();
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => $this->getShortcode($node["body"][0]["value"]),
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
    );
    return $eventArray;
  }

  public function getEventHead($id = 0)
  {
    if($id){
      $node = Node::load($id)->toArray();
    }else{
      $node = $this->getNodeByUrl();
    }

    $firstStream = array('stream' => $node["field_event_stream_provider"][0]);
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => $this->getShortcode($node["body"][0]["value"]),
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Streamers' => $this->getStreamEventList($firstStream),
      'Participants' => $this->getParticipant($node["field_event_participants"], $node['field_events_properties']),
      'sport' => $this->getSport(),
    );
    return $eventArray;
  }

  public function getLiveStreamEvents($range)
  {
    if($_GET['event']){
      if($node = Node::load($_GET['event'])){
        $events = $this->getScheduleFormat([$node->toArray()]);
      }
    }else{
      $events = $this->getScheduleLiveStreamEvents($range);
    }

    $node = $this->getNodeByUrl(1);
    $data = [
      'title' => $node->title->value,
      'description' => $node->body->value ? $this->getShortcode($node->body->value) : NULL,
      'events' => $events
    ];

    return $data;
  }

  public function getSubmenu()
  {
    $sport = $this->getSport();
    $sportName = @$sport['sportName'];
    $sportDrupalId = @$sport['sportDrupalId'];
    if (isset($sportName)) {
      $menu_link = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties(['description' => $sportDrupalId]);
      if (isset($sportName)) {
        $menu_link_id = reset($menu_link)->id();
        $menuPluginID = MenuLinkContent::load($menu_link_id)->getPluginId();
        $menu_tree = \Drupal::service('menu.link_tree');
        $parameters = new MenuTreeParameters();
        $parameters->addCondition('parent', $menuPluginID, '=')->onlyEnabledLinks();
        $tree = $menu_tree->load('main', $parameters);
        $manipulators = [
          ['callable' => 'menu.default_tree_manipulators:checkAccess'],
          ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
          ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
          ['callable' => 'toolbar_menu_navigation_links'],
        ];
        $tree = $menu_tree->transform($tree, $manipulators);
        $cacheability = new CacheableMetadata();
        foreach ($tree as $element) {
          /** @var \Drupal\Core\Menu\MenuLinkInterface $link */
          if ($element->subtree) {
            $subtree = $menu_tree->build($element->subtree);
            $output = \Drupal::service('renderer')->renderPlain($subtree);
            $cacheability = $cacheability->merge(CacheableMetadata::createFromRenderArray($subtree));
          } else {
            $output = '';
          }
        }
      }
    } else {
      $output = '';
    }
    return $output;
  }

}

