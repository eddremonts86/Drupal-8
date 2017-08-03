<?php

namespace Drupal\sesport_blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'sesport_front_stream' block.
 *
 * @Block(
 *  id = "sesport_front_stream",
 *  admin_label = @Translation("Live stream(Front Page)"),
 * )
 */
class sesport_front_stream extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'sesport_front_stream',
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
      $sportName =  $objGet->getSport();
      $nodes = $objGet->getStreamsProvider();
      $data = [
        ['sportName' => $sportName],
        ['node'=>$nodes]
      ];
      return $data ;

    }
}

