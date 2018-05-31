<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Site;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'fodbaldstreamsfootersecondaryplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsfootersecondaryplugin",
 *  admin_label = @Translation("Fodbald Streams Footer Secondary Plugin"),
 * )
 */

class FodbaldStreamsFooterSecondaryPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldstreamsfootersecondaryplugin',
      '#tags' => [],
      '#background' => [],
      '#colors' => [],
      '#front' => \Drupal::service('path.matcher')->isFrontPage()
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

