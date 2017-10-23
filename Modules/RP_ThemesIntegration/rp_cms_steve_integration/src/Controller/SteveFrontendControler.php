<?php

namespace Drupal\rp_cms_steve_integration\Controller;

use Drupal\rp_cms_steve_integration\Controller\SteveBaseControler;

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

}