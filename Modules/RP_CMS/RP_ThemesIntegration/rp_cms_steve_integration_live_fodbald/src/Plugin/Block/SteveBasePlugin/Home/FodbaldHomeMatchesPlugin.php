<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldhomematchesplugin' block.
 *
 * @Block(
 *  id = "fodbaldhomematchesplugin",
 *  admin_label = @Translation("Fodbald Home Matches Plugin"),
 * )
 */
class FodbaldHomeMatchesPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	  
	$controllerObject = new LiveFodbaldController();

    $data =  [
      '#theme' => 'fodbaldhomematchesplugin',
      '#tags' => $controllerObject->getFodbaldSchedule('block'),
      '#article' => $controllerObject->getFodbaldPreviews(10, null, 2),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getSchedule(){

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
