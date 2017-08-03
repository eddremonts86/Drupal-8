<?php

namespace Drupal\rp_api;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_api\RPAPIDescription;

class RPAPIClient extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }

    if (empty($description)) {
      $description = new Description(RPAPIDescription::getDescription());
    }

    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function getSites() {
    $response = $this->getApiSites();
    return $response['data'];
  }
  public function getRegion() {
    $response = $this->getApiRegion();
    return $response['data'];
  }
  public function getschedule(){
    $response = $this->getApischedule();
    return $response;
  }

}
