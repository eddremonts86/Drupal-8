<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Teams;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldteamsheadplugin' block.
 *
 * @Block(
 *  id = "fodbaldteamsheadplugin",
 *  admin_label = @Translation("Fodbald Teams Head Plugin"),
 * )
 */
class FodbaldTeamsHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldteamsheadplugin',
      '#tags' => $this->getEvents(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getEvents(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getFodbaldMatchData(null);	
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

