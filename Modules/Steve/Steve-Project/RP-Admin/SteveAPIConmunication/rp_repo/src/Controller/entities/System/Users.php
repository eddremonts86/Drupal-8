<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 6/28/18
 * Time: 10:48 AM
 */

namespace Drupal\rp_repo\Controller\entities\System;

use Drupal\rp_repo\Controller\entities\Generales\images;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomySteveSite;
use Drupal\rp_repo\RepoService;
use Drupal\user\Entity\User;

class Users {

  public function deleteUser($id) {
  }

  public function UpadteUser($userArray) {
    $siteObj = new taxonomySteveSite();
    $img = new  images();

    $obj = ['vid' => 'steve_site'];
    $id = $siteObj->getTaxonomyByOBj($obj, 1);
    foreach ($userArray['data']['users'] AS $userA) {
      if (isset($userA['token']) and $userA['token'] != "") {
        $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => $userA['name']]);
       if(isset($users) and !empty($users)){
          $userDrupalID = reset($users)->id();
          $user = User::load($userDrupalID);
          $picture = $img->getImg($userA['user_picture']['img_url'], $userA['user_picture']['alt'], 'user');
          $user->set('user_picture', $picture);
          $user->set('roles', $userA['roles']);
          $user->set('status', $userA['status']);
          $user->set('field_token', $userA['token']);
          $user->set('field_sites_asignated', $id);
          $user->save();
        }
        else{
          $getUser['data']['users'][]=$userA;
          $this->inesertUser($getUser);
        }
      }
    }
  }

  public function getAllUsers($sitesID) {
    $images = new images();
    $siteObj = new taxonomySteveSite();
    $all_user = [];
    $obj = ['vid' => 'steve_site', 'field_api_id' => $sitesID];
    $id = $siteObj->getTaxonomyByOBj($obj, 1);
    $userList = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['field_sites_asignated' => $id]);
    if (isset($userList) and !empty($userList)) {
      foreach ($userList as $user) {
        $I = $user->toArray();
        if ($I['name'][0]['value'] != "") {
          $all_user[] = [
            'name' => $I['name'][0]['value'],
            'pass' => $I['pass'][0]['value'],
            'mail' => $I['mail'][0]['value'],
            'timezone' => $I['timezone'][0]['value'],
            'status' => $I['status'][0]['value'],
            'roles' => $I['roles'][0]['target_id'],
            'token' => $I['field_token'][0]['value'],
            'user_picture' => [
              'img_url' => $images->getImgUrl_toexport($I['user_picture'][0]['target_id']),
              'alt' => $I['name'][0]['value'],
              'title' => $I['user_picture'][0]['title'],
              'width' => $I['user_picture'][0]['width'],
              'height' => $I['user_picture'][0]['height'],
            ],
          ];
        }
      }
    }
    return $all_user;
  }

  public function getuser() {
    $rpClient = RepoService::getClient();
    $getUser = $rpClient->getUser();
    $this->inesertUser($getUser);
    return TRUE;

  }

  /* -------------- import users in drupal json api --------- */

  public function inesertUser($getUser) {
    foreach ($getUser['data']['users'] as $user) {
      $users = \Drupal::entityTypeManager()
        ->getStorage('user')
        ->loadByProperties([
          'name' => $user['name'],
          'mail' => $user['mail'],
        ]);
      if (!$users) {
        if($user['name'] == "admin") {
          $userArray['data']['users'][] =  $user;
          $this->UpadteUser($userArray);
          continue;
        }

        $siteObj = new taxonomySteveSite();
        $tags_sites = [];
        if (isset($user['sites_asignated']) and !empty($user['sites_asignated'])) {
          foreach ($user['sites_asignated'] as $sites) {
            $obj = ['vid' => 'steve_site', 'field_api_id' => $sites['api_id']];
            $id = $siteObj->getTaxonomyByOBj($obj, 1);
            if ($id) {
              $tags_sites [] = ['target_id' => $id];
            }
          }
        }
        $img = new  images();
        $obj = [
          'name' => $user['name'],
          'mail' => $user['mail'],
          'pass' => $user['pass'],
          'field_token' => $user['token'],
          'status' => $user['status'],
          'roles' => $user['roles'],
          'field_sites_asignated' => $tags_sites,
          'user_picture' => $img->getImg($user['user_picture']['img_url'], $user['user_picture']['alt'], 'user'),
        ];
        echo 'New User - ' . $user['name'];
        echo "\n";
        $user = User::create($obj);
        $user->save();
      }
    }
    return TRUE;
  }

  public function getuserbySiteid($siteID) {
    if (!isset($siteID)) {
      $config = \Drupal::configFactory()->get('rp_base.settings');
      $siteID = $config->get('rp_base_site_api_id');
    }
    $rpClient = RepoService::getClient();
    $obj = ['site' => $siteID];
    $getUserbySite = $rpClient->getUserbySite($obj);
    $this->inesertUser($getUserbySite);
    return TRUE;

  }

  public function getUserBytoken($userToken) {
    $users = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'field_token' => $userToken,
      ]);
    if (isset($users) and !empty($users)) {
      return reset($users);
    }
    else {
      return FALSE;
    }
  }

  /* -------------- export users in json api --------- */
  public function getuserbySystemId($SiteId) {
    $usersList = $this->getUserPerSite($SiteId);
    if (isset($usersList) and !empty($usersList)) {
      return $this->getUserList($usersList);
    }
    else {
      return FALSE;
    }

  }

  public function getUserPerSite($siteID) {
    $taxonomySteveSite = new taxonomySteveSite();
    $sites = $taxonomySteveSite->getTaxonomyByOBj([
      'field_api_id' => $siteID,
      'vid' => 'steve_site',
    ], 1);
    $users = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'field_sites_asignated' => $sites,
        'status' => 1,
      ]);
    return $users;


  }

  public function getUserList($userList = NULL) {
    $all_user = [];
    if (isset($userList) and !empty($userList)) {
      $images = new images();
      foreach ($userList as $user) {
        $I = $user->toArray();
        if ($I['name'][0]['value'] != "") {
          $all_user[] = [
            'name' => $I['name'][0]['value'],
            'pass' => $I['pass'][0]['value'],
            'mail' => $I['mail'][0]['value'],
            'timezone' => $I['timezone'][0]['value'],
            'status' => $I['status'][0]['value'],
            'roles' => $I['roles'][0]['target_id'],
            'token' => $I['field_token'][0]['value'],
            'user_picture' => [
              'img_url' => $images->getImgUrl_toexport($I['user_picture'][0]['target_id']),
              'alt' => $I['name'][0]['value'],
              'title' => $I['user_picture'][0]['title'],
              'width' => $I['user_picture'][0]['width'],
              'height' => $I['user_picture'][0]['height'],
            ],
          ];
        }
      }
    }
    return $all_user;
  }

  public function getAllUsersList() {
    $all_user = [];
    $userList = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties([
        'status' => 1,
      ]);;
    if (isset($userList) and !empty($userList)) {
      $images = new images();
      foreach ($userList as $user) {
        $I = $user->toArray();
        if ($I['name'][0]['value'] != "") {
          $all_user[] = [
            'name' => $I['name'][0]['value'],
            'pass' => $I['pass'][0]['value'],
            'mail' => $I['mail'][0]['value'],
            'timezone' => $I['timezone'][0]['value'],
            'status' => $I['status'][0]['value'],
            'roles' => $I['roles'][0]['target_id'],
            'token' => $I['field_token'][0]['value'],
            'user_picture' => [
              'img_url' => $images->getImgUrl_toexport($I['user_picture'][0]['target_id']),
              'alt' => $I['name'][0]['value'],
              'title' => $I['user_picture'][0]['title'],
              'width' => $I['user_picture'][0]['width'],
              'height' => $I['user_picture'][0]['height'],
            ],
          ];
        }
      }
    }
    return $all_user;
  }

  public function getSitesAsignatedPerUser($siteID) {
    $users = $this->getUserPerSite($siteID);
    if (isset($users) and !empty($users)) {
      $taxonomySteveSite = new taxonomySteveSite();
      $images = new images();
      foreach ($users as $user) {
        $AllSite = [];
        $I = $user->toArray();
        foreach ($I['field_sites_asignated'] as $AllsitesId) {
          $sites = $taxonomySteveSite->getTaxonomyByOBj([
            'tid' => $AllsitesId['target_id'],
            'vid' => 'steve_site',
          ], 'obj');

          foreach ($sites as $newsite) {
            $AllSite[] = [
              'api_id' => $newsite->field_api_id->value,
              'site_name' => $newsite->name->value,
              'site_url' => $newsite->field_site_url->value,
              'token' => md5($newsite->field_site_token->value),
            ];
          }
        }
        if (isset($AllSite) and !empty($AllSite)) {
          $all_user[] = [
            'name' => $I['name'][0]['value'],
            'pass' => $I['pass'][0]['value'],
            'mail' => $I['mail'][0]['value'],
            'timezone' => $I['timezone'][0]['value'],
            'status' => $I['status'][0]['value'],
            'roles' => $I['roles'][0]['target_id'],
            'token' => $I['field_token'][0]['value'],
            'user_picture' => [
              'img_url' => $images->getImgUrl_toexport($I['user_picture'][0]['target_id']),
              'alt' => $I['name'][0]['value'],
              'title' => $I['user_picture'][0]['title'],
              'width' => $I['user_picture'][0]['width'],
              'height' => $I['user_picture'][0]['height'],
            ],
            'sites_asignated' => $AllSite,
          ];
        }
      }
    }
    return $all_user;
  }
}
