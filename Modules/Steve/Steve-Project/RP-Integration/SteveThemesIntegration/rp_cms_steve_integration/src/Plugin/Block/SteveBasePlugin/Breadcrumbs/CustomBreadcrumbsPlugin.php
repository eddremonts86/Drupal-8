<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Breadcrumbs;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a 'custombreadcrumbsplugin' block.
 *
 * @Block(
 *  id = "custombreadcrumbsplugin",
 *  admin_label = @Translation("Custom Breadcrumbs"),
 * )
 */
class custombreadcrumbsplugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'custombreadcrumbsplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#breadcrumbs' => $this->getInfo()
    ];
  }

  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getBreadcrumbs();
    return $data;
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