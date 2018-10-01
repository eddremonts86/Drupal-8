<?php

namespace Drupal\rp_stream_provider\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "stream_providers",
 *   description = @Translation("Stream Provider API Model"),
 *   entity = "stream_provider",
 *   api_method = "getApiStreamProviders",
 *   global_wrapper = "results"
 * )
 */
class StreamProvidersRPAPIModel extends RPAPIModelBase {

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
      'field_stream_provider_name' => [
        'api_field' => 'name'
      ],
      'field_stream_provider_type' => [
        'api_field' => 'type'
      ],
    ];
  }

}
