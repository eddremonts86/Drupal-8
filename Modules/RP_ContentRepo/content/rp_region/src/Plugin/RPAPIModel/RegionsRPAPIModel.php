<?php

namespace Drupal\rp_region\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "regions",
 *   description = @Translation("Region API Model"),
 *   entity = "region",
 *   api_method = "getApiRegion",
 *   global_wrapper = "results"
 * )
 */
class RegionsRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_region_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_region_label' => [
        'api_field' => 'label'
      ],
      'field_region_code' => [
        'api_field' => 'code'
      ],
    ];
  }

}
