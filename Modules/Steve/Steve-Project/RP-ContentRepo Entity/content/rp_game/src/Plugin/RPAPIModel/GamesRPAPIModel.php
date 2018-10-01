<?php

namespace Drupal\rp_game\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "games",
 *   description = @Translation("Games from Schedule API Model"),
 *   entity = "game",
 *   api_method = "getApiAllEvents",
 *   global_wrapper = "results"
 * )
 */
class GamesRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_game_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_game_date_plain' => [
        'api_field' => 'start'
      ],
      'field_game_date' => [
        'api_field' => 'start',
        'field_type' => 'datetime',
      ],
      'field_game_site' => [
        'api_field' => 'site',
        'source' => 'query_vars',
        'field_type' => 'entity_reference',
        'entity_type' => 'site',
        'entity_field_reference' => 'field_site_api_id',
      ],
      'field_game_region' => [
        'api_field' => 'region',
        'source' => 'query_vars',
        'field_type' => 'entity_reference',
        'entity_type' => 'region',
        'entity_field_reference' => 'field_region_code',
      ],
      'field_game_language' => [
        'api_field' => 'lang',
        'source' => 'query_vars',
        'field_type' => 'entity_reference',
        'entity_type' => 'language_content',
        'entity_field_reference' => 'field_language_content_locale',
      ],
      'field_game_sport' => [
        'api_field' => 'sport',
        'field_type' => 'entity_reference',
        'entity_type' => 'sport',
        'entity_field_reference' => 'field_sport_api_id',
        'reference_schema' => [
          'type' => 'array',
          'key' => 'id'
        ]
      ],
      'field_game_competition' => [
        'api_field' => 'competition',
        'field_type' => 'entity_reference',
        'entity_type' => 'competition',
        'entity_field_reference' => 'field_competition_api_id',
        'reference_schema' => [
          'type' => 'array',
          'key' => 'id'
        ]
      ],
      'field_game_participants' => [
        'api_field' => 'participants',
        'field_type' => 'entity_reference',
        'entity_type' => 'participant',
        'entity_field_reference' => 'field_api_id',
        'reference_schema' => [
          'type' => 'array',
          'subtype' => 'array',
          'key' => 'id'
        ]
      ],
      'field_game_stream_providers' => [
        'api_field' => 'streamprovider',
        'field_type' => 'entity_reference',
        'entity_type' => 'stream_provider',
        'entity_field_reference' => 'field_api_id',
        'reference_schema' => [
          'type' => 'array',
          'subtype' => 'array',
          'key' => 'id'
        ]
      ],
    ];
  }

  public function queryMapValues($map, $query_vars) {
    $values = parent::queryMapValues($map, $query_vars);
    if(isset($values['region'])) {
      $parts = explode('_',$values['region']);
      $values['region'] = strtoupper($parts[0]);
    }

    return $values;
  }


}
