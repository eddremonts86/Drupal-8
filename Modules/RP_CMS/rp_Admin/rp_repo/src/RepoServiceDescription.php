<?php

namespace Drupal\rp_repo;

class RepoServiceDescription {

  public static function getDescription() {
    return [
      'name' => 'RP Repo',
      'baseUrl' => 'http://cmsrepo.rebelpenguin.dk/api/v1/',
      'apiVersion' => "2017-06-02",
      'operations' => [
        'getRepoSites' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'site',
        ],
        'getRepoSite' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'site',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'query',
            ],
          ],
        ],
        'getRepoLang' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/schedule',
          'parameters' => [
            'site' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'lang' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'region' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'start' => [
              'type' => 'string',
              'location' => 'query',
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