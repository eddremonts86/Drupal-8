<?php

namespace Drupal\rp_game_info\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Game info entities.
 */
class GameInfoViewsData extends EntityViewsData {

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
