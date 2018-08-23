<?php

namespace Drupal\rp_evenets_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\Controller\entities\Pages\nodeEntities;

/**
 * Class eventsAPIController.
 */
class eventsAPIController extends ControllerBase {

  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAll() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: getAll')
    ];
  }
  /**
   * Geteventbyid.
   *
   * @return string
   *   Return Hello string.
   */
  public function geteventByID() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: geteventByID')
    ];
  }
  /**
   * Geteventsbydate.
   *
   * @return string
   *   Return Hello string.
   */
  public function geteventsByDate() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: geteventsByDate')
    ];
  }
  /**
   * Getlasteventrevisions.
   *
   * @return string
   *   Return Hello string.
   */
  public function getLastEventRevisions() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: getLastEventRevisions')
    ];
  }

}
