<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Blog;

use Drupal\Core\Block\BlockBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'SportblogHeadPlugin' block.
 *
 * @Block(
 *  id = "sportblogheadplugin",
 *  admin_label = @Translation("Sport Blog Head"),
 * )
 */
class SportblogHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sportblogheadplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => [],
      '#background' => $this->background()
    ];
  }

  public function background(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getPageBackground();
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
