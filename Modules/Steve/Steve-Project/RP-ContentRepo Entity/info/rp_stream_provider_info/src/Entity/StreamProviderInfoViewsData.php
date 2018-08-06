<?php

namespace Drupal\rp_stream_provider_info\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Stream Provider Info entities.
 */
class StreamProviderInfoViewsData extends EntityViewsData {

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
