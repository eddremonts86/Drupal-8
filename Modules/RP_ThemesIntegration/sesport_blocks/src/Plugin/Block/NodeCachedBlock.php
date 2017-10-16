<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 26-07-17
 * Time: 16:30
 */

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a Node cached block that display node's ID.
 *
 * @Block(
 *   id = "node_cached_block",
 *   admin_label = @Translation("Node Cached")
 * )
 */
class NodeCachedBlock extends BlockBase {
  public function build() {
    $build = array();
    //if node is found from routeMatch create a markup with node ID's.
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      $build['node_id'] = array(
        '#markup' => '<p>' . $node->id() . '<p>',
      );
    }
    return $build;
  }
  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}