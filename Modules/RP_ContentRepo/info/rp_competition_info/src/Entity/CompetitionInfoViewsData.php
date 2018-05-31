<?php

namespace Drupal\rp_competition_info\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Competition Info entities.
 */
class CompetitionInfoViewsData extends EntityViewsData {

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
