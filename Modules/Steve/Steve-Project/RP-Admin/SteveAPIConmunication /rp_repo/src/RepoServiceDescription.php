<?php

namespace Drupal\rp_repo;

class RepoServiceDescription {

  public static function getDescription() {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_url_api');
    return [
      'name' => 'RP Repo',
      'baseUrl' => $site_api_id.'/rpendp/',
      'apiVersion' => "2018-06-02",
      'operations' => [
        'getApiUser' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'getuser'
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
          'uri' => 'getAPIUserbyTonkesandSite/{site}/{usertoken}/{sitetoken}',
          'parameters' => [
            'site' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'usertoken' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'sitetoken' => [
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
