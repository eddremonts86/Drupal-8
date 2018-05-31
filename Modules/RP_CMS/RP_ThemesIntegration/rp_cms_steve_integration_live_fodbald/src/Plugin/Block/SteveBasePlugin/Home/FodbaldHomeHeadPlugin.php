<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldhomeheadplugin' block.
 *
 * @Block(
 *  id = "fodbaldhomeheadplugin",
 *  admin_label = @Translation("Fodbald Home Head Plugin"),
 * )
 */
class FodbaldHomeHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldhomeheadplugin',
      '#tags' => $this->getMatches(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getMatches(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getFodbaldSchedule('block', 1);	
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

