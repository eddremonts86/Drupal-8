<?php

namespace Drupal\rp_push_api\Controller\api\client;

class pushRepoServiceDescription {

  public static function getDescription() {

    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_url_api');
    return [
      'name' => 'RP Repo',
      'baseUrl' => $site_api_id.'/steveAPI/',
      'apiVersion' => "2018-06-02",
      'operations' => [
        'getTokensService' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'push/{ctoken}',
          'parameters' => [
            'ctoken' => [
              'type' => 'string',
              'location' => 'uri',
              ]
          ],
        ],
      ],
      'models' => [
        'body' => [
          'type' => 'object',
          'properties' => [
            'body' => [
              'location' => 'body',
              'type' => 'string',
            ],
          ],
        ],
        'json' => [
          'type' => 'object',
          'additionalProperties' => ['location' => 'json'],
        ],
      ],
    ];
  }
}
