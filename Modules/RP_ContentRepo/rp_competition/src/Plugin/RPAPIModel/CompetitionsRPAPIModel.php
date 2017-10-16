<?php

namespace Drupal\rp_competition\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "competitions",
 *   description = @Translation("Competitions API Model"),
 *   entity = "competition",
 *   api_method = "getApiCompetitions",
 *   global_wrapper = "results"
 * )
 */
class CompetitionsRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_competition_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_competition_logo_path' => [
        'api_field' => 'logo_path'
      ],
      'field_competition_logo_modified' => [
        'api_field' => 'logo_modified'
      ],
      'field_competition_sport' => [
        'api_field' => 'sport',
        'field_type' => 'entity_reference',
        'entity_type' => 'sport',
        'entity_field_reference' => 'field_sport_api_id'
      ],
      'field_competition_parent' => [
        'api_field' => 'parent',
        'field_type' => 'entity_reference',
        'entity_type' => 'competition',
        'entity_field_reference' => 'field_competition_api_id'
      ],
    ];
  }

}
