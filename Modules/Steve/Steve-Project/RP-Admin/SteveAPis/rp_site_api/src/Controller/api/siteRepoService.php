<?php

namespace Drupal\rp_site_api\Controller\api;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_site_api\Controller\api\siteRepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_user_api
 */

class siteRepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(siteRepoServiceDescription::getDescription());
    }
    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function getAllSite() {
    $response = $this->getAPIAllSite();
    return $response;
  }

  public function getSitebyID($parameters) {
    $response = $this->getAPISitebyID($parameters);
    return $response;
  }
}
