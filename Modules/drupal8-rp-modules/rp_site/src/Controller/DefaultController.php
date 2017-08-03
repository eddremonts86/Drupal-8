<?php

namespace Drupal\rp_site\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_api\RPAPIClient;

/**
 * Class DefaultController.
 *
 * @package Drupal\rp_site\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * Test.
   *
   * @return string
   *   Return Hello string.
   */
  public function test() {

    $rpClient  = RPAPIClient::getClient();
    $sites = $rpClient->getSites();
    foreach($sites as $site) {
      $data = array(
        'field_site_api_id' => $site['id'],
        'name' => $site['name'],
        'field_site_label' => $site['label'],
        'field_site_site_group_name' => $site['site_group_name'],
        //'field_site_regions' => ['target_id' => $site['regions'][0]]
        'field_site_languages' => is_array($site['languages']) ? implode(',',$site['languages']) : $site['languages'],
        'field_site_channels' => is_array($site['channels']) ? implode(',',$site['channels']) : $site['channels'],
      );
      $site_new = \Drupal::entityManager()->getStorage('site')->create($data);
      $site_new->save();
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('API Saved')
    ];
  }

}
