<?php

namespace Drupal\rp_sportandtournaments_api\Controller\api;

class sportsRepoServiceDescription {
  public static function getDescription() {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_url_api');
    return [
      'name' => 'RP Repo',
      'baseUrl' => $site_api_id.'/steveAPI/',
      'apiVersion' => "2018-06-02",
      'operations' => [

        'getApiUpdateSportAll' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'sports/getAll/{page}',
          'parameters' => [
            'page' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],

        'getApiUpdateSportbyid' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'sports/getSportByid/{eventid}',
          'parameters' => [
            'eventid' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],

        'getApiUpdateSportAllTranslations' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'sports/getSportTranslations/{lang}/{page}',
          'parameters' => [
            'lang'=> [
              'type' => 'string',
              'location' => 'uri',
            ],
            'page' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],

        'getApiUpdateSportTranslationByid' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'sports/getSportTranslationByid/{eventid}/{lang}',
          'parameters' => [
            'eventid' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'lang' => [
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
