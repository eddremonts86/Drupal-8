<?php

namespace Drupal\rp_user_api\Controller\api;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_user_api\Controller\api\UserRepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_user_api
 */

class UserRepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(UserRepoServiceDescription::getDescription());
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


  public function getUserBySiteID($parameters) {
    $response = $this->getAPIUserBySiteID($parameters);
    return $response;
  }

  public function getUserContentBySiteID($parameters) {
    $response = $this->getAPIUserContentBySiteID($parameters);
    return $response;
  }

  public function updateUserContentBySiteID($parameters) {
    $response = $this->updateUserAPIContentBySiteID($parameters);
    return $response;
  }






}
