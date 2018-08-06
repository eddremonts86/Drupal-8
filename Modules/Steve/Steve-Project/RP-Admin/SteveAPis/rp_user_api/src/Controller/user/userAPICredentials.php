<?php

namespace Drupal\rp_user_api\Controller\user;
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 6/29/18
 * Time: 1:49 PM
 */

/**
 *
 * @file
 * Contains \Drupal\test_api\Controller\TestAPIController.
 */



use Drupal\Core\Controller\ControllerBase;

use Drupal\rp_repo\Controller\entities\System\Users as localUser;
use Symfony\Component\HttpFoundation\Request;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use Drupal\rp_user_api\Controller\api\UserRepoService;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class userAPICredentials extends ControllerBase {

  public function getLoginCheck(Request $request) {

    $config = \Drupal::configFactory()->get('rp_base.settings');
    $siteID = $config->get('rp_base_site_api_id');
    $siteurl = $config->get('rp_base_site_url');

    $site = new taxonomySteveSite();
    $user = new  localUser();
    $siteToken = $request->attributes->get('siteToken');
    $userToken = $request->attributes->get('userToken');

    $siteData =$site->getSitebyToken($siteToken);
    $Userdata =$user->getUserBytoken($userToken);

    if($siteData and $Userdata){
      $rpClient = UserRepoService::getClient();
      $obj = [
        'site' => $siteID,
        'userToken' => $userToken,
        'siteToken' => $siteToken
      ];
      $getUserbySite = $rpClient->getUserbyTonkesandSite($obj);
      $uid= $Userdata->uid->value;
      if( $getUserbySite['data']['users']['result'] == true)
        {
          $user = User::load($uid);
          user_login_finalize($user);
          $user_destination = $siteurl.'/admin/content';
          $response = new RedirectResponse($user_destination);
          return $response;
        }
    }
    else {
      $user_destination = $siteurl;
      $response = new RedirectResponse($user_destination);
      return $response;
    }
  }
}
