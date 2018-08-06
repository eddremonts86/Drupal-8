<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Home;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldhomematchwidgetplugin' block.
 *
 * @Block(
 *  id = "sefodbaldhomematchwidgetplugin",
 *  admin_label = @Translation("Se Fodbald Home Match Widget Plugin"),
 * )
 */
class SeFodbaldHomeMatchWidgetPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController(); 
	  
    $data =  [
      '#theme' => 'sefodbaldhomematchwidgetplugin',
      '#tags' => $class->getEvents(),
      '#background' => [],
      '#colors' => []
    ];
    return $data;
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

