<?php

namespace Drupal\rp_sportandtournaments_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\rp_sportandtournaments_api\Controller\sports\exportSports;
use Drupal\rp_sportandtournaments_api\Controller\sports\importSport;

/**
 * Class generalController.
 */
class generalController extends ControllerBase {

  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAll(Request $request) {
    $page = ($page = $request->attributes->get('page')) ? $page : 0;
    $exportOBJ = new exportSports();
    $response['data'] = $exportOBJ->getAll($page);
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }

  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getsportbyid(Request $request) {
    $eventID = $request->attributes->get('eventid');
    $exportOBJ = new exportSports();
    $response['data'] = $exportOBJ->getSportbyid($eventID);
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }

  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAllTranslation(Request $request) {
    $lang = $request->attributes->get('lang');
    $page = $request->attributes->get('page');
    $exportOBJ = new exportSports();
    $data = $exportOBJ->getAlltranslations($page,$lang);
    $response['data'] = $data;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }


  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getsportbyidtranslation(Request $request) {
    $lang = $request->attributes->get('lang');
    $eventid = $request->attributes->get('eventid');
    $exportOBJ = new exportSports();
    $response['data'] = $exportOBJ->getTranslationbyID($eventid,$lang);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function sportsUpdateAllSports($page){
    $impOBJ = new importSport();
    $respond = $impOBJ->importUpdateAllSports($page);
    if($respond){
      drupal_set_message('');
      return true;
    }
    else{
      drupal_set_message('');
      return FALSE;
    }

  }

  public function sportsUpdateSportbByID($eventid){
    $impOBJ = new importSport();
    $respond = $impOBJ->importUpdateSportbByID($eventid);
    if($respond){
      drupal_set_message('');
      return true;
    }
    else{
      drupal_set_message('');
      return FALSE;
    }
  }

  public function sportsUpdateAllSportsTranslations($lang,$page){
    $impOBJ = new importSport();
    $respond = $impOBJ->importUpdateAllSportsTranslations($lang,$page);
    if($respond){
      drupal_set_message('');
      return true;
    }
    else{
      drupal_set_message('');
      return FALSE;
    }

  }

  public function sportsUpdateSportsTranslationByID($lang , $eventid){
    $impOBJ = new importSport();
    $respond = $impOBJ->importUpdateSportsTranslationByID($lang , $eventid);
    if($respond){
      drupal_set_message('');
      return true;
    }
    else{
      drupal_set_message('');
      return FALSE;
    }
  }

}
