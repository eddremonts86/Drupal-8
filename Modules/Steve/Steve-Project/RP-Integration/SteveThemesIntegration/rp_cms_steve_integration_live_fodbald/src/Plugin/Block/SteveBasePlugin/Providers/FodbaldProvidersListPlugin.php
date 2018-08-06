<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Providers;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldproviderslistplugin' block.
 *
 * @Block(
 *  id = "fodbaldproviderslistplugin",
 *  admin_label = @Translation("Fodbald Providers List Plugin"),
 * )
 */
class FodbaldProvidersListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'fodbaldproviderslistplugin',
      '#tags' => $this->getProviders(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }
  
  function getProviders(){
  	$controllerObject = new LiveFodbaldController();
  	return $controllerObject->formatedProviders();
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

