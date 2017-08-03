<?php

namespace Drupal\rp_site\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Site entities.
 */
class SiteViewsData extends EntityViewsData {

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
