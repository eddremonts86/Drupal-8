<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'ourPartners' block.
 *
 * @Block(
 *  id = "ourPartners",
 *  admin_label = @Translation("Home Page - Stream Providers List"),
 * )
 */
class sesport_ourPartners extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sesport_ourPartners',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getdata()
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

  public function getdata() {
    $objGet = new getCMSdata();
    $nodes = $objGet->getStreamsProvider();
    return $nodes;
  }
}
