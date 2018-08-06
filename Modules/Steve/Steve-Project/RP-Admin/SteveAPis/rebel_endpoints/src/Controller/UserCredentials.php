<?php

namespace Drupal\rebel_endpoints\Controller;
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

use Drupal\rp_repo\Controller\entities\System\Users as localuser;
use Symfony\Component\HttpFoundation\Request;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use Drupal\rp_repo\RepoService;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
//use \Drupal\Core\Routing\TrustedRedirectResponse
class UserCredentials extends ControllerBase {

  public function getLoginCheck(Request $request) {
    $siteToken = $request->attributes->get('siteToken');
    $site = new taxonomySteveSite();
    $siteData =$site->getSitebyToken($siteToken);

    $userToken = $request->attributes->get('userToken');
    $user = new  localuser();
    $Userdata = $user->getUserBytoken($userToken);

    if($siteData and $Userdata){
      $rpClient = RepoService::getClient();
      $config = \Drupal::configFactory()->get('rp_base.settings');
      $siteID = $config->get('rp_base_site_api_id');
      $obj = [
        'site' => $siteID,
        'usertoken' => $userToken,
        'sitetoken' => $siteToken
      ];
      $getUserbySite = $rpClient->getUserbySite($obj);
      $uid= $Userdata->uid->value;
      if($getUserbySite['data']['users'][0]['name'] == $Userdata->name->value){
        foreach ($getUserbySite['data']['users'][0]['sites_asignated'] as $site)
        {
          if($site['api_id']== $siteID){ $rediret = $site['site_url'];
            break;
          }
        }
        $user = User::load($uid);
        user_login_finalize($user);
        $user_destination = $rediret.'/admin/content';  // \Drupal::destination()->get();
        $response = new RedirectResponse($user_destination);
        return $response;
      }


    }

  }

}
