<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\StreamProvider;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'StreamProviderBodyPlugin' block.
 *
 * @Block(
 *  id = "streamproviderbodyplugin",
 *  admin_label = @Translation("Stream Provider Page Body - Streams list"),
 * )
 */
class StreamProviderBodyPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'streamproviderbodyplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo()
    ];
  }
  public function getInfo(){
    $obj = new SteveFrontendControler();
    $data = $obj->getStreamProviderFormat();
    return  $data;
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