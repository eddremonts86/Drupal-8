<?php

namespace Drupal\rp_site_api\Controller\api;

class siteRepoServiceDescription {

  public static function getDescription() {

    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_url_api');
    return [
      'name' => 'RP Repo',
      'baseUrl' => $site_api_id.'/steveAPI/',
      'apiVersion' => "2018-06-02",
      'operations' => [
        'getAPIAllSite' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getSites'
        ],
        'getAPISitebyID' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getSite/{site}',
          'parameters' => [
            'site' => [
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
