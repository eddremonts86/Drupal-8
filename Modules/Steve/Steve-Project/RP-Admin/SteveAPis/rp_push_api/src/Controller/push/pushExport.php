<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/17/18
 * Time: 4:09 PM
 */
namespace Drupal\rp_push_api\Controller\push;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\rp_push_api\Controller\pushController;

class pushExport {


  public function getSiteToken(Request $request) {
    $sitesItems =array();
    $ctoken = $request->attributes->get('ctoken');
    if (isset($ctoken)){
      $site = new pushController();
      $sites = $site->getSite($ctoken);
      foreach ($sites as $site){
          $sitesItems['site']=array(
          'client_token' => $site->client_token,
          'server_token' => $site->server_token
        );
      }
    }
    $response['data'] = $sitesItems;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

}
