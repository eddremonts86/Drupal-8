<?php

namespace Drupal\rp_base\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class RPBaseController.
 *
 * @package Drupal\rp_base\Controller
 */
class RPBaseController extends ControllerBase {

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: index')
    ];
  }

}
