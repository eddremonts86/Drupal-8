<?php

namespace Drupal\rp_repoapi;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_repoapi\RPRepoAPIDescription;

class RPRepoAPIClient extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }

    if (empty($description)) {
      $description = new Description(RPRepoAPIDescription::getDescription());
    }

    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  /**
   * Get model ids
   */
  public static function getModelIds($models) {
    $ids = [];
    foreach($models as $key => $data){
      if(isset($data['attributes']['id'])) $ids[] = $data['attributes']['id'];
    }
    return $ids;
  }

  public function getMethod($method, $parameters = [], $debug = false){
    $method = str_replace('RepoApi','',$method);
    $response = $this->{$method}($parameters);
    if($debug && function_exists('drush_print_r'))
      drush_print_r($response);
    return $response;
  }

  public function getSites() {
    $response = $this->getRepoApiSites();
    return $response['data'];
  }

  public function getSite($parameters) {
    $response = $this->getRepoApiSite($parameters);
    return $response['data'];
  }

  public function getSiteInfoConfig($parameters) {
    $response = $this->getRepoApiSiteInfoConfig($parameters);
    return $response['data'];
  }

}
