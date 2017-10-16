<?php

namespace Drupal\rp_sport\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "sports",
 *   description = @Translation("Sport API Model"),
 *   entity = "sport",
 *   api_method = "getApiSports",
 *   global_wrapper = "results"
 * )
 */
class SportsRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_sport_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_sport_code' => [
        'api_field' => 'name'
      ],
    ];
  }

}
