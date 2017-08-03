<?php

namespace Drupal\rp_cms_site_info\Plugin\RPAPIModel;

use Drupal\rp_repoapi\RPRepoAPIConfigBase;

/**
 * Provides a RPRepoAPIModel example
 * .
 * @RPAPIModel(
 *   id = "site_info",
 *   description = @Translation("CMS Sites Info Config Repo API Model (all of them)"),
 *   entity = "cms_site_info",
 *   api_method = "getRepoApiSiteInfoConfig",
 *   item_wrapper = "attributes",
 *   global_wrapper = "data"
 * )
 */
class CMSSiteInfoRPRepoAPIConfig extends RPRepoAPIConfigBase {

  /**
   * {@inheritdoc}
   */
  public function mapping() {
    return [
      'id'  => [
        'api_field' => 'id'
      ],
      'label'  => [
        'api_field' => 'label'
      ],
      'site_info'  => [
        'api_field' => 'site_info'
      ],
      'site_info_uuid' => [
        'api_field' => 'site_info_uuid'
      ],
      'site' => [
        'api_field' => 'site'
      ],
      'region' => [
        'api_field' => 'region'
      ],
      'language_content' => [
        'api_field' => 'language_content'
      ],
      'resourceFields' => [
        'api_field' => 'resourceFields'
      ],
    ];
  }


}