<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Leagues;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamsleagueslistplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsleagueslistplugin",
 *  admin_label = @Translation("Fodbald Streams Leagues List Plugin"),
 * )
 */
class FodbaldStreamsLeaguesListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$controllerObject = new LiveFodbaldStreamsController();
    $data =  [
      '#theme' => 'fodbaldstreamsleagueslistplugin',
      '#tags' => $controllerObject->getLeaguesList(), 
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

