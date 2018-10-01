<?php

namespace Drupal\rp_events_api\Controller\api;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_events_api\Controller\api\EventsRepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_user_api
 */

class EventsRepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(EventsRepoServiceDescription::getDescription());
    }
    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function updateEvents($parameters) {
    $response = $this->getApiUpdateEvents($parameters);
    return $response;
  }

  public function updateEvent($parameters) {
    $response = $this->getApiUpdateEvent($parameters);
    return $response;
  }

  public function updateEventRevision($parameters) {
    $response = $this->getApiUpdateEventRevision($parameters);
    return $response;
  }

  public function  updateEventsTranslation($parameters) {
    $response = $this->getApiUpdateEventsTranslation ($parameters);
    return $response;
  }

  public function  updateEventTranslation($parameters) {
    $response = $this->getApiUpdateEventTranslation ($parameters);
    return $response;
  }


}
