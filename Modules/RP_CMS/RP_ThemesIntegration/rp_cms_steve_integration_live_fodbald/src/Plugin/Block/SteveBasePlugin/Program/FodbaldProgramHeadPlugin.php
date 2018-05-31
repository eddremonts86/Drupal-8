<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Program;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldprogramheadplugin' block.
 *
 * @Block(
 *  id = "fodbaldprogramheadplugin",
 *  admin_label = @Translation("Fodbald Program Head Plugin"),
 * )
 */
class FodbaldProgramHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldprogramheadplugin',
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
