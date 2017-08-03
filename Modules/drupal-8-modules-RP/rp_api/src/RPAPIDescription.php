<?php

namespace Drupal\rp_api;


class RPAPIDescription {

  public static function getDescription() {
    return [
      'name' => 'RP API',
      'baseUrl' => 'http://steve.rebelpenguin.dk:10080',
      'apiVersion' => "2017-05-24",
      'operations' => [
        'getApiSites' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sites',
        ],
        'getApiSite' => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sites/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiRegion' => [
          'summary' => 'Get Regions',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/regions',
        ],
        'getApiChannel' => [
          'summary' => 'Get Channels',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/channels',
        ],
        'getApiLanguage' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/languages',
        ],
        'getApiSchedule' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/schedules',
          'parameters' => [
            // ---- Required --- ///
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
            'sport' => [
              'type' => 'string',
              'location' => 'query',
            ],

            // ---- No Required --- //
            'competition' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'type' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'tz' => [
              'type' => 'string',
              'location' => 'query',
            ],


          ],
        ],

        // New API EndPoints //

        'getApiCompetitionsbyID' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/competitions/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiSportbyID' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sports/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiStreamproviderTypesbyID' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/streamproviders/types/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiStreamprovidersbyID' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/streamproviders/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiParticipantsbyID' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/participants/{id}',
          'parameters' => [
            'id' => [
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
