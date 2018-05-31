<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 10/4/17
 * Time: 12:48 PM
 */


namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Site;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;

/**
 * Provides a 'SiteFooterPlugin' block.
 *
 * @Block(
 *  id = "sitefooterplugin",
 *  admin_label = @Translation("Site Page footer - Social Netword"),
 * )
 */
class SiteFooterPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sitefooterplugin',
      '#titulo' => 'Site Footer',
      '#descripcion' => '',
      '#tags' => $this->getInfo()
    ];
  }

  public function getInfo(){
    $ControllerObject = new SteveFrontendControler();
    $data = $ControllerObject->getSocialNetworks();
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
