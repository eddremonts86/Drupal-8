<?php

namespace Drupal\rp_region\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Region entities.
 */
class RegionViewsData extends EntityViewsData {

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
