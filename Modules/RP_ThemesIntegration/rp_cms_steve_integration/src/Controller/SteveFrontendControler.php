<?php

namespace Drupal\rp_cms_steve_integration\Controller;

use Drupal\rp_cms_steve_integration\Controller\SteveBaseControler;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\Cache\CacheableMetadata;


/**
 * Class SteveFrontendControler.
 */
class SteveFrontendControler extends SteveBaseControler {

  public function getPartners($bool) {
    $data = [
      'vid' => 'stream_provider',
      'field_stream_provider_home_promo' => $bool,
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

  public function getBlogInfo($pages) {
    return $getBlogSchedule = $this->getSchedulePager($pages);
  }

  public function getEventFullPage() {
    $node = $this->getNodeByUrl();
    $eventArray['events']= array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => @$node["body"][0]["value"],
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Participants' => $this->getParticipant($node["field_event_participants"]),
      'Streamers' => $this->getStreamEventList($node["field_event_stream_provider"]),
    );
    return $eventArray;
  }

  public function getEventParticipants() {
    $node = $this->getNodeByUrl();
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => @$node["body"][0]["value"],
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Participants' => $this->getParticipant($node["field_event_participants"]),
    );
    return $eventArray;
  }

  public function getEventStream() {
    $node = $this->getNodeByUrl();
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => @$node["body"][0]["value"],
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Streamers' => $this->getStreamEventList($node["field_event_stream_provider"]),
      'sport' => $this->getSport(),
    );
    return $eventArray;
  }

  public function getEventcontent() {
    $node = $this->getNodeByUrl();
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => @$node["body"][0]["value"],
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
    );
    return $eventArray;
  }

  public function getEventHead() {
    $node = $this->getNodeByUrl();
    $firstStream = array('stream' => $node["field_event_stream_provider"][0]);
    $eventArray['events'] = array(
      'eventName' => $node["title"][0]["value"],
      'eventbody' => @$node["body"][0]["value"],
      'eventApiId' => $node["field_event_api_id"][0]["value"],
      'eventDate' => $node["field_event_date"][0]["value"],
      'Streamers' => $this->getStreamEventList($firstStream),
      'Participants' => $this->getParticipant($node["field_event_participants"]),
      'sport' => $this->getSport(),
    );
    return $eventArray;
  }

  public function getLiveStreamEvents($range){

      return $this->getScheduleLiveStreamEvents($range);

  }

  public function getSubmenu() {
    $sport = $this->getSport();
    $sportName = @$sport['sportName'];
    $sportDrupalId = @$sport['sportDrupalId'];
    if(isset($sportName)){
      $menu_link = \Drupal::entityTypeManager()->getStorage('menu_link_content')->loadByProperties(['menu_name' => 'main', 'description' => $sportDrupalId]);
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
        }
        else {
          $output = '';
        }
      }
      return $output;
    }
    else{return [];}

  }

}