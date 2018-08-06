<?php

namespace Drupal\rp_user_api\Controller\api;

class UserRepoServiceDescription {

  public static function getDescription() {

    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_url_api');
    return [
      'name' => 'RP Repo',
      'baseUrl' => $site_api_id.'/steveAPI/',
      'apiVersion' => "2018-06-02",
      'operations' => [
        'getApiUser' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getUsers'
        ],
        'updateUserAPIContentBySiteID' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'updateUser/{SiteId}',
          'parameters' => [
            'SiteId' => [
              'type' => 'string',
              'location' => 'uri',
              ]
          ],
        ],
        'getAPIUserBySiteID' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getUser/{site}',
          'parameters' => [
            'site' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],
        'getAPIUserContentBySiteID' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getUserContent/{site}',
          'parameters' => [
              'site' => [
                'type' => 'string',
                'location' => 'uri',
              ]
          ],
        ],
        'getApiUserbySite' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getuserbySiteId/{site}',
          'parameters' => [
            'site' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],
        'getAPIUserbyTonkesandSite' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getAPIUserbyTonkesandSite/{site}/{userToken}/{siteToken}',
          'parameters' => [
            'site' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'userToken' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'siteToken' => [
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
