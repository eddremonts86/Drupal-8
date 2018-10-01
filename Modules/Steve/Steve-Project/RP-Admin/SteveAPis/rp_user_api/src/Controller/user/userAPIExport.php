<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/7/18
 * Time: 1:05 PM
 */

namespace Drupal\rp_user_api\Controller\user;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\rp_repo\Controller\entities\System\Users;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use  Drupal\rp_repo\Controller\entities\Generales\api;


class userAPIExport extends api {

  /**
   * Getallusers.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAllUsers(Request $request) {
    $user = new Users();
    $response = [];
    $response['data']['users'] = $user->getAllUsersList();
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  /**
   * Getuserbyid.
   *
   * @return string
   *   Return Hello string.
   */
  public function getUserById(Request $request) {
    $SiteId = $request->attributes->get('SiteId');
    $user = new Users();
    $response = [];
    $response['data']['users'] = $user->getuserbySystemId($SiteId);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getUserContent(Request $request) {
    $SiteId = $request->attributes->get('SiteId');
    $user = new Users();
    $response = [];
    $response['data']['users'] = $user->getSitesAsignatedPerUser($SiteId);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  public function getuserbySiteId(Request $request) {
    $SiteId = $request->attributes->get('SiteId');
    $user = new Users();
    $response = [];
    $response['data']['users'] = $user->getuserbySystemId($SiteId);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }


  /**
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   */
  public function getAPIUserbyTonkesandSite(Request $request) {

    $SiteId = $request->attributes->get('site');
    $siteToken = $request->attributes->get('siteToken');
    $userToken = $request->attributes->get('userToken');
    $siteObj = new taxonomySteveSite();
    $sites = $siteObj->getTaxonomyByOBj([
      'field_api_id' => $SiteId,
      'field_site_token' => $siteToken,
      'vid' => 'steve_site',
    ], 1);

    if($sites){
      $users = \Drupal::entityTypeManager()
        ->getStorage('user')
        ->loadByProperties([
          'field_sites_asignated' => $sites,
          'field_token' => $userToken
        ]);

      if($users){
        $response['data']['users'] =array(
          'result' => true
        );
        $response['method'] = 'GET';
        return new JsonResponse($response);
      }
      else{
        $response['data']['users'] =array('result' => false);
        $response['method'] = 'GET';
        return new JsonResponse($response);
      }
    }
    else{
      $response['data']['users'] =array('result' => false);
      $response['method'] = 'GET';
      return new JsonResponse($response);
    }
  }

  public function getAllUsersToUpdate(Request $request) {
    $user = new Users();
    $response = [];
    $SiteId = $request->attributes->get('SiteId');
    $response['data']['users'] = $user->getAllUsers($SiteId);
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }


}
