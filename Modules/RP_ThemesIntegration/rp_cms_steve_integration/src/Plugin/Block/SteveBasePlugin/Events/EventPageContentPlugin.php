<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Events;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'EventPageContentPlugin' block.
 *
 * @Block(
 *  id = "eventpagecontentplugin",
 *  admin_label = @Translation("Event Page Content"),
 * )
 */
class EventPageContentPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'eventpagecontentplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo(),
    ];
  }

  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getEventcontent();
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
