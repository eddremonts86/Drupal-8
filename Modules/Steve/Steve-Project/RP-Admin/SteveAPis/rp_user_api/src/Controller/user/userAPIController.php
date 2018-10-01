<?php

namespace Drupal\rp_user_api\Controller\user;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class userAPIController.
 */
class userAPIController extends ControllerBase {



  /**
   * Importallusers.
   *
   * @return string
   *   Return Hello string.
   */
  public function importAllUsers() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: importAllUsers')
    ];
  }

  /**
   * Importuserbyid.
   *
   * @return string
   *   Return Hello string.
   */
  public function importUserbyID() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: importUserbyID')
    ];
  }



}
