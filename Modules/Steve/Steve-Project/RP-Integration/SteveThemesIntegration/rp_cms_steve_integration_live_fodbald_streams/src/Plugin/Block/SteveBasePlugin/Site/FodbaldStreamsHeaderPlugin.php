<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Site;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamsheaderplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsheaderplugin",
 *  admin_label = @Translation("Fodbald Streams Header Plugin"),
 * )
 */
class FodbaldStreamsHeaderPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController();
	
	$modificators = ['rp_cms_steve_integration_live_fodbald_streams.leagues' => 'FodbaldStreamsMenuLeaguesLink', 'rp_cms_steve_integration_live_fodbald_streams.teams' => 'FodbaldStreamsMenuTeamsLink'];
	 
    $data =  [
      '#theme' => 'fodbaldstreamsheaderplugin',
      '#tags' => $class->buildMenu('fodbaldstreamsmenu', $modificators),
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

