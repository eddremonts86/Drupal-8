<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Team;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldteamfeaturesplugin' block.
 *
 * @Block(
 *  id = "fodbaldteamfeaturesplugin",
 *  admin_label = @Translation("Fodbald Team Features Plugin"),
 * )
 */
class FodbaldTeamFeaturesPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldteamfeaturesplugin',
      '#tags' => $this->getInfo(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
   public function getInfo(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getTeamPageInfo();	
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

