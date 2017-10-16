<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Cache\Cache;


use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'sesporttopmenu' block.
 *
 * @Block(
 *  id = "sesporttopmenu",
 *  admin_label = @Translation("Sesport - Top Menu(General Pages)"),
 * )
 */
class sesport_topMenu extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sesport_topMenu',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getdata(),
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
    $output = $objGet->getSubmenu();
    $data = ['output' => $output];
    return $data;
  }
}

