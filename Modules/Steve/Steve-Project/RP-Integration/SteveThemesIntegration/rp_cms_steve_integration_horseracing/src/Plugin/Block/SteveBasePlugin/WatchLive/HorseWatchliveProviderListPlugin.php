<?php

namespace Drupal\rp_cms_steve_integration_horseracing\Plugin\Block\SteveBasePlugin\WatchLive;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'horsewatchliveproviderlistplugin' block.
 *
 * @Block(
 *  id = "horsewatchliveproviderlistplugin",
 *  admin_label = @Translation("Horse Watch Live Provider List Plugin"),
 * )
 */
class HorseWatchLiveProviderListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'horsewatchliveproviderlistplugin',
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

