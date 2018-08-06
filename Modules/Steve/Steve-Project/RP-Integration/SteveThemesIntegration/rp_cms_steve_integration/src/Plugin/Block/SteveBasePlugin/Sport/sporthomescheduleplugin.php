<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Sport;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'sporthomescheduleplugin' block.
 *
 * @Block(
 *  id = "sporthomescheduleplugin",
 *  admin_label = @Translation("Sport Page - schedule"),
 * )
 */
class sporthomescheduleplugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sporthomescheduleplugin',
      '#titulo' => 'Site Footer',
      '#descripcion' => '',
      '#tags' => $this->getInfo(),
      '#colors' => $this->getColors()
    ];
  }
  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getSchedulePlusTree(0);
    return $data;
  }
  public function getColors(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getColors();
    return $data;
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
