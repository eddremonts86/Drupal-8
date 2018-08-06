<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Match;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldmatchprovidersplugin' block.
 *
 * @Block(
 *  id = "fodbaldmatchprovidersplugin",
 *  admin_label = @Translation("Fodbald Match Providers Plugin"),
 * )
 */
class FodbaldMatchProvidersPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$controllerObject = new LiveFodbaldController();

    $data =  [
      '#theme' => 'fodbaldmatchprovidersplugin',
      '#tags' => $controllerObject->getFodbaldMatchData('node', 0, TRUE),
      '#article' => $controllerObject->getFodbaldPreview(),
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

