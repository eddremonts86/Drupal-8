<?php

namespace Drupal\rp_site\Plugin\RPAPIModel;

use Drupal\rp_repoapi\RPRepoAPIModelBase;

/**
 * Provides a RPRepoAPIModel example
 * .
 * @RPAPIModel(
 *   id = "repo_sites",
 *   description = @Translation("Sites Repo API Model (all of them)"),
 *   entity = "site",
 *   api_method = "getRepoApiSites"
 * )
 */
class SitesRPRepoAPIModel extends RPRepoAPIModelBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'field_site_api_id' => [
        'api_field' => 'id'
      ],
      'name' => [
        'api_field' => 'name'
      ],
      'field_site_label' => [
        'api_field' => 'label'
      ],
      'field_site_site_group_name' => [
        'api_field' => 'site_group_name'
      ],
      'field_site_regions' => [
        'api_field' => 'regions',
        'field_type' => 'entity_reference',
        'entity_type' => 'region',
        'entity_field_reference' => 'field_region_code',
        'required' => true
      ],
      'field_site_languages' => [
        'api_field' => 'languages',
        'field_type' => 'entity_reference',
        'entity_type' => 'language_content',
        'entity_field_reference' => 'field_language_content_locale',
        'required' => true
      ],
      'field_site_channels' => [
        'api_field' => 'channels',
        'field_type' => 'entity_reference',
        'entity_type' => 'channel',
        'entity_field_reference' => 'field_api_id',
        'required' => true
      ],
    ];
  }

}
