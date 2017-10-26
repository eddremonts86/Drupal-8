<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Events;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'EventPageFooterPlugin' block.
 *
 * @Block(
 *  id = "eventpagefooterplugin",
 *  admin_label = @Translation("Event Page Footer"),
 * )
 */
class EventPageFooterPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'eventpagefooterplugin',
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
