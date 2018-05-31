<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Site;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'fodbaldfootersecondaryplugin' block.
 *
 * @Block(
 *  id = "fodbaldfootersecondaryplugin",
 *  admin_label = @Translation("Fodbald Footer Secondary Plugin"),
 * )
 */

class FodbaldFooterSecondaryPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldfootersecondaryplugin',
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

