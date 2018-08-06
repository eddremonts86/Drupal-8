<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamshomematchesplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamshomematchesplugin",
 *  admin_label = @Translation("Fodbald Streams Home Matches Plugin"),
 * )
 */
class FodbaldStreamsHomeMatchesPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	  
	$class = new LiveFodbaldStreamsController();
    $data =  [
      '#theme' => 'fodbaldstreamshomematchesplugin',
      '#tags' => $class->getSchedulePlusTree(0, "Y-m-d", 5, 0, $class->getSport(2, 'api'), NULL, NULL, ['FodbaldStreamsScheduleFormatModificator']),
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
