<?php

namespace Drupal\rp_api;


class RPAPIDescription {

  public static function getDescription() {
    return [
      'name' => 'RP API',
      'baseUrl' => 'http://steve.rebelpenguin.dk:8080',
      'apiVersion' => "2017-05-24",
      'operations' => [
        'getApiSites' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/site',
        ],
        'getApiRegion' => [
          'summary' => 'Get Region',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/region',
        ],
        'getApischedule' => [
          'summary' => 'Get Region',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/schedule?site=1&lang=da_DK&region=DK&start=2017-05-23',
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