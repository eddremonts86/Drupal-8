<?php

namespace Drupal\rp_sport\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Sport entities.
 */
class SportViewsData extends EntityViewsData {

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
