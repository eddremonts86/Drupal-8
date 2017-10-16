<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 10/4/17
 * Time: 4:05 PM
 */

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Home;

use Drupal\Core\Block\BlockBase;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'HomeFooterPlugin' block.
 *
 * @Block(
 *  id = "homefooterplugin",
 *  admin_label = @Translation("Home Page Footer"),
 * )
 */

class HomeFooterPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'homefooterplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => []
    ];
  }


}