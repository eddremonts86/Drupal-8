<?php

namespace Drupal\rp_events_api\Controller\api;

class EventsRepoServiceDescription {

  public static function getDescription() {

    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_url_api');
    return [
      'name' => 'RP Repo',
      'baseUrl' => $site_api_id.'/steveAPI/',
      'apiVersion' => "2018-06-02",
      'operations' => [
        'getApiUpdateEvents' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'events/{date}/{page}',
          'parameters' => [
            'date' => [
              'type' => 'string',
              'location' => 'uri',
              ],
            'page' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],
        'getApiUpdateEvent' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'event/{eventid}',
          'parameters' => [
            'eventid' => [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],

        'getApiUpdateEventsTranslation' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'eventsTranslaion/{date}/{page}/{lang}',
          'parameters' => [
            'date' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'page' => [
              'type' => 'string',
              'location' => 'uri',
            ],
            'lang'=> [
              'type' => 'string',
              'location' => 'uri',
            ]
          ],
        ],
        'getApiUpdateEventTranslation' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'eventsTranslaion/{eventid}/{lang}',
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

        'getApiUpdateEventRevision' => [
          'summary' => 'Get Sites',
          'responseModel' => 'json',
          'httpMethod' => 'GET',
          'uri' => 'eventreviews/{eventid}',
          'parameters' => [
            'eventid' => [
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
