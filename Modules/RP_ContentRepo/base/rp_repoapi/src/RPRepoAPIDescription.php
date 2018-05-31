<?php

namespace Drupal\rp_repoapi;


class RPRepoAPIDescription {

  public static function getDescription() {
    return [
      'name' => 'RP RepoAPI',
      'baseUrl' => 'http://cmsrepo.rebelpenguin.dk',
      'apiVersion' => "v1",
      'operations' => [
        'getRepoApiSites'    => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/v1/site',
        ],
        'getRepoApiSite'     => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/v1/site',
          'parameters' => [
            'filter[id][value]' => [
              'type' => 'string',
              'location' => 'query'
            ],
          ]
        ],
        'getRepoApiSiteInfoConfig'     => [
          'summary' => 'Get Config Site Info',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/v1/site_info_config',
          'parameters' => [
            'filter[site][value]' => [
              'type' => 'string',
              'location' => 'query'
            ],
          ]
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
