<?php

namespace Drupal\rp_cms_steve_integration_horseracing\Plugin\Block\SteveBasePlugin\Reviews;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'horsereviewsheadplugin' block.
 *
 * @Block(
 *  id = "horsereviewsheadplugin",
 *  admin_label = @Translation("Horse Reviews Head Plugin"),
 * )
 */
class HorseReviewsHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'horsereviewsheadplugin',
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

