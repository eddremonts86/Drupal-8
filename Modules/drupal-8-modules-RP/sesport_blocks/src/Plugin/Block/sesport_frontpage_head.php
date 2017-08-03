<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\sesport_blocks\Controller\getCMSdata;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sesportfrontpagehead' block.
 *
 * @Block(
 *  id = "sesportfrontpagehead",
 *  admin_label = @Translation("sesport front page head"),
 * )
 */
class sesport_frontpage_head extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
          '#theme' => 'sesport_frontpage_head',
          '#titulo' => 'Mi titulo sesportblocks',
          '#descripcion' => 'Mi descripción sesportblocks',
          '#tags' => $this->getdata(),
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
        $nodes = $objGet->getSchedule(1,$sportName['sportApiId']);
        $data = [
          ['sportName' => $sportName['sportName']],
          ['sportApiId' => $sportName['sportApiId']],
          ['node'=>$nodes]
        ];
        return $data ;
    }
}

