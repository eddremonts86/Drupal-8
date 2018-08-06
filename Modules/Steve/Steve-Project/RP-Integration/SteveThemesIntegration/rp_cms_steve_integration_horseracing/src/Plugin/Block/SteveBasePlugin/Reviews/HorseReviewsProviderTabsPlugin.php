<?php

namespace Drupal\rp_cms_steve_integration_horseracing\Plugin\Block\SteveBasePlugin\Reviews;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'horsereviewsprovidertabsplugin' block.
 *
 * @Block(
 *  id = "horsereviewsprovidertabsplugin",
 *  admin_label = @Translation("Horse Reviews Provider Tabs Plugin"),
 * )
 */
class HorseReviewsProviderTabsPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'horsereviewsprovidertabsplugin',
      '#tags' => [],
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }

  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }
}

