<?php

namespace Drupal\rp_push_api\Controller\api\server;

class pushServerRepoServiceDescription {

  public static function getDescription($clientSite) {
    return [
      'name' => 'RP Repo',
      'baseUrl' => $clientSite.'/steveAPI/',
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
        'pushAPIContentsbyid' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'push/{s_token}/{c_token}/{c_type}/{apiid}',
          'parameters' => [
            's_token' => [
              'type' => 'string',
              'location' => 'uri',
              ],
            'c_token' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'c_type' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'apiid' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'pushAPIContents' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'push/{s_token}/{c_token}/{c_type}',
          'parameters' => [
            's_token' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'c_token' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'c_type' => [
              'type' => 'string',
              'location' => 'uri',
            ],
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
