<?php

namespace Drupal\rp_api;


class RPAPIDescription
{

 public static function getDescription()
  {
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $rp_base_site_api = $config->get('rp_base_site_api');
    if (isset($rp_base_site_api) or empty($rp_base_site_api) or $rp_base_site_api == ' ' ){
      $rp_base_site_api = "http://steveapi.rebelpenguin.dk:8000";
    }
    return [
      'name' => 'RP API',
      'baseUrl' => $rp_base_site_api,
      'apiVersion' => "2017-05-24",
      'operations' => [

        'getApiTimezones' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/timezones',
        ],
        'getApiTimezone' => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/timezones/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],

        'getApiRegions' => [
          'summary' => 'Get Regions',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/regions',
        ],
        'getApiRegion' => [
          'summary' => 'Get Regions',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/regions/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],

        'getApiChannels' => [
          'summary' => 'Get Channels',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/channels',
        ],
        'getApiChannel' => [
          'summary' => 'Get Channels',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/channels/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],

        'getApiLanguages' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/languages',
        ],
        'getApiLanguage' => [
          'summary' => 'Get Language content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/languages/{id}',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],

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

        'getApiSiteChannel' => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sites/{id}/channels',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiSiteLanguges' => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sites/{id}/languages',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiSiteRegions' => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sites/{id}/regions',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],
        'getApiSiteStreams' => [
          'summary' => 'Get Site',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sites/{id}/streamproviders',
          'parameters' => [
            'id' => [
              'type' => 'string',
              'location' => 'uri',
            ],
          ],
        ],



/*--------------------------------------------------------------*/
        'getApiSchedule' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/schedules.json',
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
            'include_participants' => [
              'type' => 'string',
              'location' => 'query',
            ],
            // ---- No Required --- //
            'sport' => [
              'type' => 'string',
              'location' => 'query',
            ],
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
            'include_locales' => [
              'type' => 'string',
              'location' => 'query',
            ],


          ],
        ],
        'getApiSports' => [
          'summary' => 'Get Sport content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/sports',
        ],
        'getApiStreamProviders' => [
          'summary' => 'Get Stream Provider content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/streamproviders',
        ],
        'getApiCompetitions' => [
          'summary' => 'Get Competitions content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/competitions',
          'parameters' => [
            'sport' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'page' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'include_locales' => [
              'type' => 'string',
              'location' => 'query',
            ]
          ],
        ],
        'getApiParticipants' => [
          'summary' => 'Get Participants content',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/participants',
          'parameters' => [
            'competition' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'search' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'sport' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'page' => [
              'type' => 'string',
              'location' => 'query',
            ],
            'include_locales' => [
              'type' => 'string',
              'location' => 'query',
            ]
          ],
        ],
        'getApiGamesSchedule' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/schedules.json',
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
            'include_locales' => [
              'type' => 'string',
              'location' => 'query',
            ]
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
            ], 'include_locales' => [
              'type' => 'string',
              'location' => 'query',
            ]
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
            ], 'include_locales' => [
              'type' => 'string',
              'location' => 'query',
            ]
          ],
        ],
        'getApiAllEvents' => [
          'summary' => 'Get API Schedule',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'api/events',
          'parameters' => [
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
