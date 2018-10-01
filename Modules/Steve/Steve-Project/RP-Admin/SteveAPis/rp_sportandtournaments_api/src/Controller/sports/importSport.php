<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/5/18
 * Time: 11:26 AM
 */
namespace Drupal\rp_sportandtournaments_api\Controller\sports;


use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_rollbar\rollbarReport;
use Drupal\rp_sportandtournaments_api\Controller\api\sportsRepoService;

class importSport {


  public function importUpdateAllSports($page){
    $api = sportsRepoService::getClient();
    $data = array(
      'page'=>$page
    );
    $sportArray = $api->updateAllSports($data);
    $sportOBJ = new taxonomyTournament();
    if(count($sportArray['data']['term']['terminos']) > 0){
      foreach ($sportArray['data']['term']['terminos'] as $sport)
      {$sportOBJ->updateTaxonomy($sport);}
      drupal_set_message('---------------------------- Page '.$sportArray['data']['next'].' ----------------------------');
      if ($sportArray['data']['next'] != null){
        $this->importUpdateAllSports($sportArray['data']['next']);
      }
      return true;
    }
    else{
      drupal_set_message("Don't have more information to update");
    }

  }

  public function importUpdateSportbByID($eventid){
    $api = sportsRepoService::getClient();
    $data = array(
      'eventid' => $eventid
    );
    $sport = $api->updateSportbByID($data);
    if(!empty($sport['data']['term']['terminos']))
    {
      $sportOBJ = new taxonomyTournament();
      foreach ($sport['data']['term']['terminos'] as $obj){
      $sportOBJ->updateTaxonomy($obj);}
    }
    return true;
  }

  public function importUpdateAllSportsTranslations($lang,$page){
    $api = sportsRepoService::getClient();
    $data = array(
      'lang'=> $lang,
      'page'=> $page
    );
    $sport = $api->updateAllSportsTranslations($data);
    $this->translate($sport['data']['term']['terminos'],$lang);
      if ($sport['data']['next'] != "null"){
        $this->importUpdateAllSportsTranslations($lang , $sport['data']['next']);
      }
    return true;
  }

  public function importUpdateSportsTranslationByID($lang , $eventid){
    $api = sportsRepoService::getClient();
    $data = array(
      'lang'=> $lang,
      'eventid'=> $eventid,
    );
    $sport = $api->updateSportsTranslationByID($data);
    $this->translate($sport['data']['term']['terminos'],$lang);
  return TRUE;
  }

  public function translate($sport,$lang){
    $sportOBJ = new taxonomyTournament();
    foreach ($sport as $obj){
       $sportOBJ->updateTrnaslation($obj,$lang);}
       return true;
    }


}

