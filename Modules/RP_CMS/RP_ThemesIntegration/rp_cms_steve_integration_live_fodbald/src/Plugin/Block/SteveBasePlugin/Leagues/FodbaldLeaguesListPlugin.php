<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Leagues;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldleagueslistplugin' block.
 *
 * @Block(
 *  id = "fodbaldleagueslistplugin",
 *  admin_label = @Translation("Fodbald Leagues List Plugin"),
 * )
 */
class FodbaldLeaguesListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldleagueslistplugin',
      '#tags' => $this->getList(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getList(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getLeaguesList(); 
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

