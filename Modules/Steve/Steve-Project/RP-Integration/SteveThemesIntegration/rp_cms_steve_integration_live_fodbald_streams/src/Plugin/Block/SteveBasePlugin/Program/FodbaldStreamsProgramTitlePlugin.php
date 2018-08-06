<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Program;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'fodbaldstreamsprogramtitleplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsprogramtitleplugin",
 *  admin_label = @Translation("Fodbald Streams Program Title Plugin"),
 * )
 */
class FodbaldStreamsProgramTitlePlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldstreamsprogramtitleplugin',
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
