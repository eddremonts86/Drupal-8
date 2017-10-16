<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'sesportfrontpagetopevents' block.
 *
 * @Block(
 *  id = "sesportfrontpagetopevents",
 *  admin_label = @Translation("Sesport - Top events(Sport Page)"),
 * )
 */
class sesport_frontpage_topevents extends BlockBase
{

    /**
     * {@inheritdoc}
     */
  public function build()
    {
        return [
          '#theme' => 'sesport_frontpage_topevents',
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
    $data = $objGet->getTopEvents();
    return $data ;
  }
}
