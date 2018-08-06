<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 10/24/17
 * Time: 4:45 PM
 */

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Site;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'SiteSubMenuPlugin' block.
 *
 * @Block(
 *  id = "sitesubmenuplugin",
 *  admin_label = @Translation("Site Sport Sub Menu"),
 * )
 */

class SiteSubMenuPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sitesubmenuplugin',
      '#titulo' => 'Site Sport Sub Menu ',
      '#descripcion' => '',
      '#tags' => $this->getInfo()
    ];
  }

  public  function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getSubmenu();
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
