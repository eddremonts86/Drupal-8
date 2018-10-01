<?php

namespace Drupal\rp_push_api\Controller\api\client;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Drupal\rp_push_api\Controller\api\client\pushRepoServiceDescription;

/**
 * Class RepoService.
 *
 * @package Drupal\rp_user_api
 */

class pushRepoService extends GuzzleClient {

  public static function getClient(Client $client = NULL, Description $description = NULL, array $config = []) {
    if (empty($client)) {
      $client = new Client();
    }
    if (empty($description)) {
      $description = new Description(pushRepoServiceDescription::getDescription());
    }
    return new self($client, $description, NULL, NULL, NULL, $config);
  }

  public function getTokens($parameters) {
    $response = $this->getTokensService($parameters);
    return $response;
  }



}
