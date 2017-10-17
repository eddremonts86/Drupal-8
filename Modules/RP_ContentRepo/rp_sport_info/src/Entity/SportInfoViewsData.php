<?php

namespace Drupal\rp_sport_info\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Sport info entities.
 */
class SportInfoViewsData extends EntityViewsData {

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
