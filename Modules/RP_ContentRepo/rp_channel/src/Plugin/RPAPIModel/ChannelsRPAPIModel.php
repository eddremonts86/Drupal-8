<?php

namespace Drupal\rp_channel\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "channels",
 *   description = @Translation("Channel API Model"),
 *   entity = "channel",
 *   api_method = "getApiChannel",
 *   global_wrapper = "results"
 * )
 */
class ChannelsRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_channel_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_channel_code' => [
        'api_field' => 'code'
      ],
    ];
  }

}
