<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'sesport_blog' block.
 *
 * @Block(
 *  id = "sesport_blog",
 *  admin_label = @Translation("Sesport_blog"),
 * )
 */
class sesport_blog extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $data = $this->getdata();
    $element = 0 ;

    $build['result'] =[
                      '#theme' => 'sesport_blog',
                      '#titulo' => 'Blog',
                      '#descripcion' => 'Desc',
                      '#tags' =>  $data
                      ];
    $build['pager'] = [
                       '#type' => 'pager',
                       '#element' => $element,
                       '#tags' => array('<<','<','','>','>>')
                      ];
    return $build;
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
    $index = $_GET['page'];
    $index = $objGet->getClearNumbers($index);
    $sportName =  $objGet->getSport();
    if(!isset($index)){$index = 0;}
    $nodes = $objGet->getBlogBySport(10,$index,$sportName,$format = "Y-m-d");
    $data = [['node'=>$nodes]];
    return $nodes ;

  }
}
