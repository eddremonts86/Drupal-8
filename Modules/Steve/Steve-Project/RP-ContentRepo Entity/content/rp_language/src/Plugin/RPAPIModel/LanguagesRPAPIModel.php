<?php

namespace Drupal\rp_language\Plugin\RPAPIModel;

use Drupal\rp_api\RPAPIModelBase;

/**
 * Provides a RPAPIModel example
 * .
 * @RPAPIModel(
 *   id = "languages",
 *   description = @Translation("Language content API Model"),
 *   entity = "language_content",
 *   api_method = "getApiLanguage",
 *   global_wrapper = "results"
 * )
 */
class LanguagesRPAPIModel extends RPAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_language_content_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_language_content_code' => [
        'api_field' => 'name'
      ],
      'field_language_content_locale' => [
        'api_field' => 'locale'
      ],
    ];
  }

}
