<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Blog;

use Drupal\Core\Block\BlockBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'SportBlogBodyPlugin' block.
 *
 * @Block(
 *  id = "sportblogbodyplugin",
 *  admin_label = @Translation("Sport Blog Body"),
 * )
 */
class SportBlogBodyPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $data = $this->getinfo();
    $element = 0 ;

    $build['result'] =[
      '#theme' => 'sportblogbodyplugin',
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
  public function getinfo(){
    $objGet = new SteveFrontendControler();
    $nodes = $objGet->getBlogInfo(10);
    return $nodes ;
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
