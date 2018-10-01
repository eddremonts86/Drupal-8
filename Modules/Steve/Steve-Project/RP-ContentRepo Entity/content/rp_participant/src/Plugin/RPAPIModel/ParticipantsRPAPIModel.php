<?php

namespace Drupal\rp_participant\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "participants",
 *   description = @Translation("Participants API Model"),
 *   entity = "participant",
 *   api_method = "getApiParticipants",
 *   global_wrapper = "results"
 * )
 */
class ParticipantsRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_participant_logo_path' => [
        'api_field' => 'logo_path'
      ],
      'field_participant_logo_modified' => [
        'api_field' => 'logo_modified'
      ],
      'field_participant_sport' => [
        'api_field' => 'sport',
        'field_type' => 'entity_reference',
        'entity_type' => 'sport',
        'entity_field_reference' => 'field_sport_api_id'
      ],
    ];
  }

}
