<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 10/4/17
 * Time: 4:04 PM
 */

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
use Drupal\Core\Cache\Cache;
/**
 * Provides a 'HomePartnerPlugin' block.
 *
 * @Block(
 *  id = "homepartnerplugin",
 *  admin_label = @Translation("Home Page Partners"),
 * )
 */

class HomePartnerPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'homepartnerplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getPartnerList()
    ];
  }
  public function getPartnerList(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getPartners(1);
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