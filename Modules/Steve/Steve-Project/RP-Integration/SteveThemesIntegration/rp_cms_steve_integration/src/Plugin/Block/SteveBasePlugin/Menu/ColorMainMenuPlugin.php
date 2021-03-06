<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Menu;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'ColorMainMenuPlugin' block.
 *
 * @Block(
 *  id = "colormainmenuplugin",
 *  admin_label = @Translation("Color Main Menu Block"),
 * )
 */
class ColorMainMenuPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'colormainmenuplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo(),
    ];
  }

  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getMenuLinks();
    return $data ;
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
