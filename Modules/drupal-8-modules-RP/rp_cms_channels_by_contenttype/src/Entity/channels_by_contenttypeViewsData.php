<?php

namespace Drupal\rp_cms_channels_by_contenttype\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Channels_by_contenttype entities.
 */
class channels_by_contenttypeViewsData extends EntityViewsData {

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
