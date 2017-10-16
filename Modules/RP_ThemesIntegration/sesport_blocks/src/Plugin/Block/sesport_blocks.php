<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;
/**
 * Provides a 'sesportblocks' block.
 *
 * @Block(
 *  id = "sesportblocks",
 *  admin_label = @Translation("Sesport- Live Stream(PARTNERS - by name)"),
 * )
 */
class sesport_blocks extends BlockBase
{

    /**
     * {@inheritdoc}
     */
  public function build()
    {
        return [
            '#theme' => 'sesport_blocks',
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
    $nodes = $objGet->getStreamsProvider();
    return $nodes ;
  }
}

