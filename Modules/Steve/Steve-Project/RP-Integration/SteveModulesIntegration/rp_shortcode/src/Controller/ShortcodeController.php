<?php

namespace Drupal\rp_shortcode\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class ShortcodeController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $build = [
      '#theme' => 'shortcodepage',
    ];
    return $build;
  }

}