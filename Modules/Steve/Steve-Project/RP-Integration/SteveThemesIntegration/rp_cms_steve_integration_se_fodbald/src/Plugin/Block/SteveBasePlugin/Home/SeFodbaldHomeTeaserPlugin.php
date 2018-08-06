<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldhometeaserplugin' block.
 *
 * @Block(
 *  id = "sefodbaldhometeaserplugin",
 *  admin_label = @Translation("Se Fodbald Home Teaser Plugin"),
 * )
 */
class SeFodbaldHomeTeaserPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'sefodbaldhometeaserplugin',
      '#tags' => [],
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

