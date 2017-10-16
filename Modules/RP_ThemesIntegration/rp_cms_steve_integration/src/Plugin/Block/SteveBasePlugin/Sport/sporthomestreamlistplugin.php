<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Sport;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'sporthomestreamlistplugin' block.
 *
 * @Block(
 *  id = "sporthomestreamlistplugin",
 *  admin_label = @Translation("Sport Page streamlist"),
 * )
 */
class sporthomestreamlistplugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sporthomestreamlistplugin',
      '#titulo' => 'Site Footer',
      '#descripcion' => '',
      '#tags' => []
    ];
  }
}
