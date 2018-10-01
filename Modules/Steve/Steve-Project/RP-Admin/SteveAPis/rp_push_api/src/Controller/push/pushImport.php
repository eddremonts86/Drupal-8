<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/17/18
 * Time: 4:20 PM
 */

namespace Drupal\rp_push_api\Controller\push;
use Drupal\rp_push_api\Controller\api\client\pushRepoService;
use Drupal\rp_push_api\Controller\pushController;
use function PHPSTORM_META\elementType;
use Symfony\Component\HttpFoundation\Request;
use Drupal\rp_events_api\Controller\eventsAPIController;


class pushImport {

  public function importSiteConfig($ctoken){
    $siteAPI = pushRepoService::getClient();
    $obj=array('ctoken' => $ctoken);
    $siteArray = $siteAPI->getTokens($obj);
    if(isset($siteArray)) {
      $config_factory = \Drupal::configFactory();
      $config = $config_factory->getEditable('rp_base.settings');
      $config->set('rp_base.push_server_token', $siteArray['data']['site']['server_token'])->save();
      $config->set('rp_base.push_client_token', $siteArray['data']['site']['client_token'])->save();
    }
    return true;
  }

  public function pushInfo(Request $request){
    $server_token = $request->attributes->get('s_token');
    $client_token = $request->attributes->get('c_token');
    $content_type = strtolower( $request->attributes->get('c_type'));
    $apiID = $request->attributes->get('apiid');
    if($this->credential($server_token,$client_token,$content_type)){
      if(isset($apiID) and $content_type == 'events'){
        $evente = new eventsAPIController();
        $data =  $evente->geteventByID($apiID);
        return $data;
      }
      else if(!isset($apiID) and $content_type == 'events'){
        $evente = new eventsAPIController();
        $data =  $evente->geteventByID($apiID);
        return $data;
      }
      else{
        return TRUE;
      }
    }
    else return [] ;

  }

  public function credential($server_token,$client_token,$content_type){
    $config_factory = \Drupal::configFactory();
    $config = $config_factory->getEditable('rp_base.settings');
    $pushController = new  pushController();
    $content_type = $pushController ->getEntityConf('client_'.$content_type);
    if( $client_token == $config->get('rp_base.push_client_token') and  $server_token == $config->get('rp_base.push_server_token') and $content_type == "1")  return TRUE;
    else return FALSE;
  }


}
