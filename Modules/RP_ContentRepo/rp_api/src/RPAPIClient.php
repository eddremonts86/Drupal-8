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

  /**
   * Get model ids
   */
  public static function getModelIds($models) {
    $ids = [];
    foreach ($models as $key => $data) {
      if (isset($data['id'])) {
        $ids[] = $data['id'];
      }
    }
    return $ids;
  }

  public function getMethod($method, $parameters = [], $debug = FALSE) {
    $method = str_replace('Api', '', $method);
    $response = $this->{$method}($parameters);
    if ($debug && function_exists('drush_print_r')) {
      drush_print_r($response);
    }
    return $response;
  }

  public function getSites() {
    $response = $this->getApiSites();
    return $response;
  }

  public function getSite($parameters) {
    $response = $this->getApiSite($parameters);
    return $response;
  }

  public function getRegion() {
    $response = $this->getApiRegion();
    return $response;
  }

  public function getChannel() {
    $response = $this->getApiChannel();
    return $response;
  }

  public function getLanguage() {
    $response = $this->getApiLanguage();
    return $response;
  }

  public function getSports() {
    $response = $this->getApiSports();
    return $response;
  }

  public function getStreamProviders() {
    $response = $this->getApiStreamProviders();
    return $response;
  }

  public function getCompetitions($parameters) {
    $response = $this->getApiCompetitions($parameters);
    return $response;
  }

  public function getParticipants($parameters) {
    $response = $this->getApiParticipants($parameters);
    return $response;
  }

  public function getGamesSchedule($parameters) {
    $response = $this->getApiGamesSchedule($parameters);
    return $response;
  }

  public function getschedule($parameters) {
    $response = $this->getApiSchedule($parameters);
    return $response;
  }

  public function getCompetitionsbyID($parameters) {
    $response = $this->getApiCompetitionsbyID($parameters);
    return $response;
  }

  public function getSportbyID($parameters) {
    $response = $this->getApiSportbyID($parameters);
    return $response;
  }

  public function getStreamproviderTypesbyID($parameters) {
    $response = $this->getApiStreamproviderTypesbyID($parameters);
    return $response;
  }

  public function getStreamprovidersbyID($parameters) {
    $response = $this->getApiStreamprovidersbyID($parameters);
    return $response;
  }

  public function getParticipantsbyID($parameters) {
    $response = $this->getApiParticipantsbyID($parameters);
    return $response;
  }

}
