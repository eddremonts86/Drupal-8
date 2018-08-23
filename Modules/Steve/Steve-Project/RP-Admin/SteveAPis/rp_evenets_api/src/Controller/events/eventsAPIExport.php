<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 8/10/18
 * Time: 3:56 PM
 */

namespace Drupal\rp_evenets_api\Controller\events;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\Controller\entities\Pages\nodeEntities;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class eventsAPIExport extends ControllerBase{

  /**
   * Getall.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAllNodes(Request $request) {
    $date = $request->attributes->get('date');
    $page = $request->attributes->get('page');
    if (!isset($page)){$page = 0;}
    $events = new nodeEntities();
    $nodes = $events->getNodes($page,$date,null,'events');
    $response['data'] = $nodes;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }
  /**
   * Geteventbyid.
   *
   * @return string
   *   Return Hello string.
   */
  public function geteventByID(Request $request) {
    $eventAPIId = $request->attributes->get('eventid');
    $events = new nodeEntities();
    $node = $events->getNodes(0,null,$eventAPIId,'events');
    $response['data'] = $node;
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }


  public function geteventrevisionByID(Request $request) {
    $eventAPIId = $request->attributes->get('eventid');
    $events = new nodeEntities();
    $node = $events->getNodeReviw($eventAPIId,'events');
    $response['data'] = $node;
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }


}
