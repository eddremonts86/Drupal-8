<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Sport;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'sporthomefooterplugin' block.
 *
 * @Block(
 *  id = "sporthomefooterplugin",
 *  admin_label = @Translation("Sport Page footer"),
 * )
 */
class sporthomefooterplugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sporthomefooterplugin',
      '#titulo' => 'Site Footer',
      '#descripcion' => '',
      '#tags' => $this->getPartnerList()
    ];
  }
  public function getPartnerList(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getSport();
    return $data ;
  }

}