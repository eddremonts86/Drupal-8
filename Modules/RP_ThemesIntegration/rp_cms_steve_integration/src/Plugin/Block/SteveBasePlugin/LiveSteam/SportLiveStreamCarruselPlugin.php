<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\LiveSteam;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a 'SportLiveStreamCarruselPlugin' block.
 *
 * @Block(
 *  id = "sportlivestreamcarruselplugin",
 *  admin_label = @Translation("Sport Live Stream Page Top"),
 * )
 */
class SportLiveStreamCarruselPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sportlivestreamcarruselplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo(),
    ];
  }

  public function getInfo() {
    $objGet = new SteveFrontendControler();
    $data = $objGet->getLiveStreamEvents(3);
    //$objGet->getPHP_Var_Dump($data);
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
