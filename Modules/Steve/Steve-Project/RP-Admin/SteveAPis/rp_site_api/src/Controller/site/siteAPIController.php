<?php

namespace Drupal\rp_site_api\Controller\site;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\taxonomy\Entity\Term;



use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;

/**
 * Class siteAPIController.
 */
class siteAPIController extends ControllerBase {



  /**
   * Getallsite.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAllSite(Request $request) {
    $Site = new taxonomySteveSite();
    $response['data']['sites'] =  $Site->getSites();
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }

  /**
   * Getsitebyid.
   *
   * @return string
   *   Return Hello string.
   */
  public function getSitebyID(Request $request) {
    $SiteId = $request->attributes->get('site');
    $Site = new taxonomySteveSite();
    $response['data']['site'] = $Site->getSites($SiteId);
    $response['method'] = 'GET';
    return new JsonResponse($response);

  }



}
