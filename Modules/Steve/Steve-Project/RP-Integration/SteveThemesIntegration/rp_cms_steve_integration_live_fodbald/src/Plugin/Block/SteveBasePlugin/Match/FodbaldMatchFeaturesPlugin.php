<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Match;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldmatchfeaturesplugin' block.
 *
 * @Block(
 *  id = "fodbaldmatchfeaturesplugin",
 *  admin_label = @Translation("Fodbald Match Features Plugin"),
 * )
 */
class FodbaldMatchFeaturesPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldmatchfeaturesplugin',
      '#tags' => $this->getTeams(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getTeams(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getFodbaldMatchData('node');	
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

