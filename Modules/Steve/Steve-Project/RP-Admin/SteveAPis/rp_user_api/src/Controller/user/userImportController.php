<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 7/26/18
 * Time: 3:21 PM
 */

namespace Drupal\rp_user_api\Controller\user;

use Drupal\rp_user_api\Controller\api\UserRepoService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_repo\Controller\entities\System\Users as UserSteve;
use Drupal\rp_repo\Controller\entities\Generales\support;

class userImportController extends ControllerBase{

    public function getAllUsers(){
      $rpClient = UserRepoService::getClient();
      $userOBJ =  $rpClient->getUser();
      $user = new UserSteve();
      $user->inesertUser($userOBJ);

      return true;
    }

    public function getUserBySiteID($siteId){
      $siteId = array('site' => $siteId);
      $rpClient = UserRepoService::getClient();
      $userOBJ = $rpClient->getUserBySiteID($siteId);
      $user = new UserSteve();
      $user->inesertUser($userOBJ);
      return true;
    }

    public function getUserContentBySiteID($siteId){
      $siteId = array('site' => $siteId);
      $rpClient = UserRepoService::getClient();
      $userOBJ = $rpClient->getUserContentBySiteID($siteId);
      $user = new UserSteve();
      $user->inesertUser($userOBJ);

      return true;
    }

    public function getUserSiteDefault(){
      $config = \Drupal::configFactory()->get('rp_base.settings');
      $site_api_id = $config->get('rp_base_site_api_id');
      $siteId = array('site' => $site_api_id);
      $rpClient = UserRepoService::getClient();
      $userOBJ = $rpClient->getUserContentBySiteID($siteId);
      $user = new UserSteve();
      $user->inesertUser($userOBJ);
      return true;

    }


  public function updateUserSiteDefault(){
    $config = \Drupal::configFactory()->get('rp_base.settings');
    $site_api_id = $config->get('rp_base_site_api_id');
    $support = new support();
    $siteId = array('SiteId' => $support->getClearUrl($site_api_id));
    $rpClient = UserRepoService::getClient();
    $userOBJ = $rpClient->updateUserContentBySiteID($siteId);
    $user = new UserSteve();
    $user->UpadteUser($userOBJ);
    return true;

  }


}
