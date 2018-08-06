<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Program;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldprogrammatchlistplugin' block.
 *
 * @Block(
 *  id = "fodbaldprogrammatchlistplugin",
 *  admin_label = @Translation("Fodbald Program Match List Plugin"),
 * )
 */
class FodbaldProgramMatchListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

	$class = new LiveFodbaldController();
    $data =  [
      '#theme' => 'fodbaldprogrammatchlistplugin',
      '#tags' => $class->getFodbaldSchedulePage(),
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
