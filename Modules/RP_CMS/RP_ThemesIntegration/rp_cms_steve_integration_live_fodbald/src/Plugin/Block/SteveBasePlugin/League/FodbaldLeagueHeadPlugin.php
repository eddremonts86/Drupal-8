<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\League;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldleagueheadplugin' block.
 *
 * @Block(
 *  id = "fodbaldleagueheadplugin",
 *  admin_label = @Translation("Fodbald League Head Plugin"),
 * )
 */
class FodbaldLeagueHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldleagueheadplugin',
      '#tags' => $this->getEvents(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getEvents(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getFodbaldMatchData('term');	
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

