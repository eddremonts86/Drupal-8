<?php

namespace Drupal\rp_repo;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_repo\RepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_repo
 */

class RepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(RepoServiceDescription::getDescription());
    }
    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function getUserbySite($parameters) {
    $response = $this->getApiUserbySite($parameters);
    return $response;
  }

  public function getUserbyTonkesandSite($parameters) {
    $response = $this->getAPIUserbyTonkesandSite($parameters);
    return $response;
  }

  public function getUser() {
    $response = $this->getApiUser();
    return $response;
  }
}
