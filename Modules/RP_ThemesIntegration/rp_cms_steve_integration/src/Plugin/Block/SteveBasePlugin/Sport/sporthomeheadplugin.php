<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Sport;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a 'sporthomeheadplugin' block.
 *
 * @Block(
 *  id = "sporthomeheadplugin",
 *  admin_label = @Translation("Sport Page Top"),
 * )
 */
class sporthomeheadplugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'sporthomeheadplugin',
      '#titulo' => 'Site Footer',
      '#descripcion' => '',
      '#tags' => $this->getInfo()
    ];
    return $data;

  }
  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getSchedule(1);
    return $data ;
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
