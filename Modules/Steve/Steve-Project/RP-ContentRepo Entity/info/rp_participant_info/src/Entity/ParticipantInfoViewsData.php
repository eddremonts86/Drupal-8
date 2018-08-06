<?php

namespace Drupal\rp_participant_info\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Participant Info entities.
 */
class ParticipantInfoViewsData extends EntityViewsData {

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
