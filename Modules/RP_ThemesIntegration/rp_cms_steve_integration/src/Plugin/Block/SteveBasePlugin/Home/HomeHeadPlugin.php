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
/**
 * Provides a 'HomeHeadPlugin' block.
 *
 * @Block(
 *  id = "homeheadplugin",
 *  admin_label = @Translation("Home Page Top/head"),
 * )
 */
class HomeHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'homeheadplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => []
    ];
  }

}