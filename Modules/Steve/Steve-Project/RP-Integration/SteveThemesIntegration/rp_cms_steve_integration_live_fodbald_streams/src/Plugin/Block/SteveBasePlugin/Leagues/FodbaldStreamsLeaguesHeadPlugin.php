<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Leagues;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamsleaguesheadplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsleaguesheadplugin",
 *  admin_label = @Translation("Fodbald Streams Leagues Head Plugin"),
 * )
 */
class FodbaldStreamsLeaguesHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController();
    $data =  [
      '#theme' => 'fodbaldstreamsleaguesheadplugin',
      '#tags' => $class->getLeaguesList(),
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

