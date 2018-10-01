<?php

namespace Drupal\rp_push_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/*Api Steve Classes  */
use Drupal\rp_events_api\Controller\eventsAPIController;
use Drupal\rp_push_api\Controller\api\server\pushServerRepoService;
use Cocur\Slugify\Slugify;

/* Class pushController.
 */
class pushController extends ControllerBase {

  public function getlocalcontenttype(){
    $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
    $contentTypesList = [];
    foreach ($contentTypes as $contentType) {
      $contentTypesList['node'][$contentType->id()] = $contentType->label();
    }

    $database = \Drupal::database();
    $taxonomyList = $database->select('taxonomy_term_field_data', 'x')->fields('x', array('vid'))->groupBy('vid')->execute()->fetchAll();
    foreach ($taxonomyList as $taxonomy) {
      $contentTypesList['taxonomy'][$taxonomy->vid] = $taxonomy->vid;
    }
    return $contentTypesList;
  }

  public function getEntityConf($entityName){
    $entityName = $this->cleanKey($entityName);
    $database = \Drupal::database();
    $entityName = strtolower($entityName);
    $entityName = str_replace(' ', '_', $entityName);
    $exit = $database->select('steve_push_api_entity_config', 'x')
      ->fields('x', array('active'))
      ->condition('x.entity_type', $entityName, '=')
      ->execute()->fetchAll();
    if (isset($exit[0]->active)){
       $exit = ($exit[0]->active == 1) ? '1' :'0';
    }
    else{
      $exit = 'off';
    }
    return $exit;
  }

  public function getSiteList(){
    $database = \Drupal::database();
    $exit = $database->select('steve_push_api', 'x')
      ->fields('x', array('client_name','server_token', 'client_token', 'active'))
      ->execute()->fetchAll();
    if (!empty($exit)) {
      return $exit;
    }
    return false;
  }

  public function getSite($ClientToken){
    $database = \Drupal::database();
    $exit = $database->select('steve_push_api', 'x')
      ->fields('x', array('client_name','server_token', 'client_token', 'active'))
      ->condition('x.client_token', $ClientToken, '=')
      ->execute()->fetchAll();
    if (!empty($exit)) {
      return $exit;
    }
    return false;
  }

  public function renderSiteTable()
  {
    $results = $this->getSiteList();
    $data = array();
    if (is_array($results) and !empty($results)) {
      foreach ($results as $result) {
          if($result->active == 0 ){
            $bText = 'Enabled';
            $bvalue = 1;
          }
          else{
            $bText = 'Desabled';
            $bvalue = 0;

          }
          $data[] = array(
          'siteName' => ['#markup' => $this->t('<a href="'.$result->client_name. '">' . $result->client_name . '</a>')],
          'cToken' => $result->client_token,
          'sToken' => $result->server_token,
//          'active' => $result->active,
          'OPERATIONS' => ['#markup' => $this->t('<a href="/admin/push_api/'. $result->client_token.'/'.$result->server_token.'/'.$bvalue.'" class="button js-form-submit form-submit">'.$bText.'</a>')]
        );
      }
    }
    return $data;
  }

  public function insertConfg($key,$value){
        $key = $this->cleanKey($key);
        $database = \Drupal::database();
        $entyti = $database->select('steve_push_api_entity_config', 'x')
          ->fields('x', array('active', 'entity_type'))
          ->condition('x.entity_type', $key, '=')
          ->execute()->fetchAll();
        if(!empty($entyti)){
          $result = $database->update('steve_push_api_entity_config')
            ->fields(['active' => $value,])
            ->condition('entity_type', $key, '=')
            ->execute();
        }
        else{
          $result = $database->insert('steve_push_api_entity_config')
            ->fields([
              'entity_type' => $key,
              'active' => $value,
            ])->execute();
        }
    return true;
  }

  public function insertSite($SiteName , $ClientToken, $ServerToken){
        $database = \Drupal::database();
        $SiteName  =  $SiteName;
        $ClientToken   = $ClientToken;
        $ServerToken  = $ServerToken;
        $entyti = $database->select('steve_push_api', 'x')
          ->fields('x', array('active'))
          ->condition('x.client_name', $SiteName, '=')
          ->condition('x.server_token', $ServerToken, '=')
          ->condition('x.client_token', $ClientToken, '=')
          ->execute()->fetchAll();
        if(!empty($entyti)){
          drupal_set_message('This site is on the system, please check the list below.');
         return FALSE;
        }
        else{
          $result = $database->insert('steve_push_api')
            ->fields([
              'client_name' => $SiteName,
              'server_token' => $ServerToken,
              'client_token' => $ClientToken,
              'active' => 1,
            ])->execute();
        }
    return true;
  }

  public function updateSiteList(Request $request){
    $client_token = $request->attributes->get('cToken');
    $server_token = $request->attributes->get('sToken');
    $active = $request->attributes->get('active');
     $database = \Drupal::database();
     $result = $database->update('steve_push_api')
       ->fields(['active' => $active,])
       ->condition('server_token', $server_token, '=')
       ->condition('client_token', $client_token, '=')
       ->execute();
    $response = new RedirectResponse('/admin/push_api/config');
    return $response;
    }

  public function pushRequest($data){
    $siteslist = $this->getSiteList();
    if(is_array($siteslist)) {
      foreach ($siteslist as $site) {
        $siteAPI = pushServerRepoService::getClient(NULL, NULL, [], $site->client_name);
        $data['s_token'] = $site->server_token;
        $data['c_token'] = $site->client_token;
        $d =  $siteAPI->pushContentsbyid($data);
        var_dump($d);
        exit();
      }
    }
    return TRUE;
  }

  public function cleanKey($key){
     $key = strtolower($key);
     $key = str_replace(' ', '_', $key);
     $slugify = new  Slugify();
     $key = $slugify->slugify($key);
    return $key;
   }

}

