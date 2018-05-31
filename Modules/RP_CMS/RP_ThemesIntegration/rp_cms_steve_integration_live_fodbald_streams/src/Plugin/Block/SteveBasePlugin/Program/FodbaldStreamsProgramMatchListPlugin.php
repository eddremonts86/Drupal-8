<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Program;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamsprogrammatchlistplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsprogrammatchlistplugin",
 *  admin_label = @Translation("Fodbald Streams Program Match List Plugin"),
 * )
 */
class FodbaldStreamsProgramMatchListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController();
	
    $data =  [
      '#theme' => 'fodbaldstreamsprogrammatchlistplugin',
      '#tags' => $class->getFodbaldStreamsSchedule('program', 5, 40),
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
