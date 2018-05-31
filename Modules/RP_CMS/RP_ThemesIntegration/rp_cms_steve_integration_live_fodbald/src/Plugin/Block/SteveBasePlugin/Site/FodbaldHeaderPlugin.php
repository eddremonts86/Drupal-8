<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Site;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'fodbaldheaderplugin' block.
 *
 * @Block(
 *  id = "fodbaldheaderplugin",
 *  admin_label = @Translation("Fodbald Header Plugin"),
 * )
 */
class FodbaldHeaderPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
  	$class = new LiveFodbaldController();
    $data =  [
      '#theme' => 'fodbaldheaderplugin',
      '#tags' => $class->getFodbaldMenu(),
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

