<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\LiveSteam;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a 'SportLiveStreamScheduleFooterPlugin' block.
 *
 * @Block(
 *  id = "sportlivestreamschedulefooterplugin",
 *  admin_label = @Translation("Sport Live Stream Page Footer"),
 * )
 */
class SportLiveStreamScheduleFooterPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sportlivestreamschedulefooterplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo(),
    ];
  }

  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getSchedulePlusTree(50);
    //$objGet->getPHP_Var_Dump($data);
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
