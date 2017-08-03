<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'sesport_livestream_carrousel' block.
 *
 * @Block(
 *  id = "sesport_livestream_carrousel",
 *  admin_label = @Translation("Live Stream Carrousel"),
 * )
 */
class sesport_livestream_carrousel extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sesport_livestream_carrousel',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getdata()
    ];
  }
  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
  public function getdata()
  {
    $objGet = new getCMSdata();
    $data = $objGet->getLiveStreamTopEvents();
    return $data ;
  }

}
