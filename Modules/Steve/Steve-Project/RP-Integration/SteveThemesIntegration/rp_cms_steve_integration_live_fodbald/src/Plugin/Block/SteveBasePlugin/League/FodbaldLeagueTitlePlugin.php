<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\League;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldleaguetitleplugin' block.
 *
 * @Block(
 *  id = "fodbaldleaguetitleplugin",
 *  admin_label = @Translation("Fodbald League Title Plugin"),
 * )
 */
class FodbaldLeagueTitlePlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldleaguetitleplugin',
      '#tags' => $this->getInfo(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
   public function getInfo(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getLeaguePageInfo();	
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

