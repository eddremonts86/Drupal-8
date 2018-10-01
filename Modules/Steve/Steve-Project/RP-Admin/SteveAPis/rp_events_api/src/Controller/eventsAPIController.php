<?php

namespace Drupal\rp_events_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_events_api\Controller\events\eventAPIImport;
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
  public function getAll($date , $page) {

    $import = new eventAPIImport();
    $import->getAll($date , $page);

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
  public function geteventByID($id) {

    $import = new eventAPIImport();
    $titleList = $import->geteventByID($id);
    var_dump($titleList);
    exit();
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Node updated - '.$titleList)
    ];
  }




  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAllTranslaion($date , $page, $lang) {

    $import = new eventAPIImport();
    $import->getAllTranslaion($date , $page,$lang);

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
  public function geteventTranslaionByID($id,$lang) {

    $import = new eventAPIImport();
    $import->geteventTranslaionByID($id,$lang);

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
