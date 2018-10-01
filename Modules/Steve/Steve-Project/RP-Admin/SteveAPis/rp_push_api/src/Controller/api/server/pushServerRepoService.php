<?php

namespace Drupal\rp_push_api\Controller\api\server;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_push_api\Controller\api\server\pushServerRepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_user_api
 */

class pushServerRepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = [],$clientSite) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(pushServerRepoServiceDescription::getDescription($clientSite));
    }
    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function getTokens($parameters) {
    $response = $this->getTokensService($parameters);
    return $response;
  }

  public function pushContents($parameters) {
    $response = $this->pushAPIContents($parameters);
    return $response;
  }

  public function pushContentsbyid($parameters) {
    $response = $this->pushAPIContentsbyid($parameters);
    return $response;
  }



}
