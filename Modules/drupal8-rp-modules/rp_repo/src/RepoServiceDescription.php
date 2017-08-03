<?php

namespace Drupal\rp_repo;

class RepoServiceDescription {
  public static function getDescription() {
    return [
      'name' => 'RP Repo',
      'baseUrl' => 'http://repo.br/api/',
      'apiVersion' => "2017-06-02",
      'operations' => [
        'getRepoSites' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'site?_format=json',
        ]
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