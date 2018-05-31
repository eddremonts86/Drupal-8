<?php

namespace Drupal\rp_api;


class RPAPIDescription
{

    public static function getDescription()
    {
        // new API server http://steveapi.rebelpenguin.dk:8000;

        return [
            'name' => 'RP API',
            'baseUrl' => 'http://steveapi.rebelpenguin.dk:8000',
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
                        'include_participants'=>[
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
