<?php

namespace Drupal\rp_client_site\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Client_site entities.
 */
class client_siteViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
