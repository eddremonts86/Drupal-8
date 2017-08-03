<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;


/**
 * Provides a 'sesport_head_gamepage' block.
 *
 * @Block(
 *  id = "sesport_head_gamepage",
 *  admin_label = @Translation("Head Game Page"),
 * )
 */
class sesport_head_gamepage extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return [
      '#theme' => 'sesport_head_gamepage',
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
    $nodes = $objGet->getGamePage();
    $data = [
      ['sportName' => $sportName['sportName']],
      ['node'=>$nodes],
      ['sportApiId' => $sportName['sportApiId']]
    ];

   /* echo "<pre>"; var_dump($nodes); echo "</pre>";
    exit();*/

    return $data ;
  }

}
