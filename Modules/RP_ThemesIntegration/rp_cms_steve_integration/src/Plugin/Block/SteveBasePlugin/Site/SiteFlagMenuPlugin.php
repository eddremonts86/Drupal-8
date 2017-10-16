<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Site;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'SiteFlagMenuPlugin' block.
 *
 * @Block(
 *  id = "siteflagmenuplugin",
 *  admin_label = @Translation("Site Page Top"),
 * )
 */
class SiteFlagMenuPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'siteflagmenuplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => []
    ];
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
