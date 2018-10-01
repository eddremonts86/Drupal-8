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

  public function getMethod($method, $parameters = [], $debug = FALSE, $recursive = TRUE) {
    $bucket = [];
    $method = str_replace('Api', '', $method);
    $response = $this->{$method}($parameters);
    if ($debug && function_exists('drush_print_r')) {
      drush_print_r($response);
    }

    //var_dump($response);
    if ($recursive && isset($response['next']) && !is_null($response['next'])) {
      $page = str_replace('page=', '', parse_url($response['next'], PHP_URL_QUERY));
      if (!empty($page)) {
        $parameters['page'] = $page;
        $bucket = $this->getRecursiveMethod($method, $parameters, $debug, $bucket);
      }
    }
    if (!empty($bucket)) {
      $response['results'] = $bucket;
    }

    return $response;
  }

  public function getRecursiveMethod($method, $parameters, $debug, $bucket) {

    $response = $this->getMethod($method, $parameters, $debug, FALSE);
    if (isset($response['next']) && !is_null($response['next'])) {
      $page = str_replace('page=', '', parse_url($response['next'], PHP_URL_QUERY));
      $parameters['page'] = $page;
      foreach ($response['results'] as $id => $data) {
        $bucket[] = $data;
      }

      $bucket = $this->getRecursiveMethod($method, $parameters, $debug, $bucket);
    }

    return $bucket;


  }


  public function getSites() {
    $response = $this->getApiSites();
    return $response;
  }

  public function getSite($parameters) {
    $response = $this->getApiSite($parameters);
    return $response;
  }

  public function getSiteChannel($parameters) {
    $response = $this->getApiSiteChannel($parameters);
    return $response;
  }

  public function getSiteLanguges($parameters) {
    $response = $this->getApiSiteLanguges($parameters);
    return $response;
  }

  public function getSiteRegions($parameters) {
    $response = $this->getApiSiteRegions($parameters);
    return $response;
  }

  public function getSiteStreams($parameters) {
    $response = $this->getApiSiteStreams($parameters);
    return $response;
  }

  public function getRegions() {
    $response = $this->getApiRegions();
    return $response;
  }

  public function getRegion($parameters) {
    $response = $this->getApiRegion($parameters);
    return $response;
  }

  public function getChannels() {
    $response = $this->getApiChannels();
    return $response;
  }

  public function getChannel($parameters) {
    $response = $this->getApiChannel($parameters);
    return $response;
  }

  public function getLanguage($parameters) {
    $response = $this->getApiLanguage($parameters);
    return $response;
  }

  public function getLanguages() {
    $response = $this->getApiLanguages();
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

  public function getTimezones() {
    $response = $this->getApiTimezones();
    return $response;
  }

  public function getTimezone($parameters) {
    $response = $this->getApiTimezone($parameters);
    return $response;
  }

  public function getParticipantsbyID($parameters) {
    $response = $this->getApiParticipantsbyID($parameters);
    return $response;
  }

  public function getAllevents($parameters) {
    if (!$parameters) {
      $parameters = date('Y-m-26');
    }
    $parameters = ['start' => $parameters];
    $response = $this->getApiAllEvents($parameters);
    return $response;
  }

}
