<?php

namespace Drupal\rp_sportandtournaments_api\Controller\api;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_sportandtournaments_api\Controller\api\sportsRepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_user_api
 */

class sportsRepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(sportsRepoServiceDescription::getDescription());
    }
    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function updateAllSports($parameters) {
    $response = $this->getApiUpdateSportAll($parameters);
    return $response;
  }

  public function updateSportbByID($parameters) {
    $response = $this->getApiUpdateSportbyid($parameters);
    return $response;
  }

  public function  updateAllSportsTranslations($parameters) {
    $response = $this->getApiUpdateSportAllTranslations ($parameters);
    return $response;
  }

  public function  updateSportsTranslationByID($parameters) {
    $response = $this->getApiUpdateSportTranslationByid ($parameters);
    return $response;
  }


}
