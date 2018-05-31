<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldhometabsplugin' block.
 *
 * @Block(
 *  id = "fodbaldhometabsplugin",
 *  admin_label = @Translation("Fodbald Home Tabs Plugin"),
 * )
 */
class FodbaldHomeTabsPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldhometabsplugin',
      '#tags' => $this->getInfo(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  public function getInfo(){
	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getFodbaldHomeTabs();
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
