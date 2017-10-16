<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 10/4/17
 * Time: 4:05 PM
 */

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'HomeBodyPlugin' block.
 *
 * @Block(
 *  id = "homebodyplugin",
 *  admin_label = @Translation("Home Page body"),
 * )
 * @MigrateProcessPlugin(
 *  id = "homebodyplugin"
 *   )
 */
class HomeBodyPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'homebodyplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => [],
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
}