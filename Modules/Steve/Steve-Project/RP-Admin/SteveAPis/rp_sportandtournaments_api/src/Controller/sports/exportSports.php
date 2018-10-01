<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/5/18
 * Time: 11:26 AM
 */

namespace Drupal\rp_sportandtournaments_api\Controller\sports;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use  Drupal\rp_repo\Controller\entities\Generales\api;


class exportSports extends api{

  public function getAll($page){
    $taxonomyOBJ = new taxonomyTournament();
    $terms = $taxonomyOBJ->getAllTerm('sport',$page);
    return $taxonomyOBJ->leaguesFormater($terms);
  }

  public function getSportbyid($sportID){
    $taxonomyOBJ = new taxonomyTournament();
    $terms = $taxonomyOBJ->getAllTerm('sport',0,null,$sportID);

    if(isset($terms)){
      return $taxonomyOBJ->leaguesFormater($terms);
    }
    else {
       $i = new  rollbarReport();
       $i->error("Sport/Tournament API ID '".$sportID."'  don't exist in this systems. Please check http://steveapi.rebelpenguin.dk:8000/api/competitions/'.$sportID.'");
       return null;
       }
  }

  public function getAlltranslations($page,$lang){
    $taxonomyOBJ = new taxonomyTournament();
    $terms = $taxonomyOBJ->getAllTerm('sport',$page,$lang);
    if(isset($terms)){
      return $taxonomyOBJ->leaguesFormater($terms,$lang);
    }
    else {return null;}
  }

  public function getTranslationbyID($sportID,$lang){
    $taxonomyOBJ = new taxonomyTournament();
    $terms = $taxonomyOBJ->getAllTerm('sport',0,$lang,$sportID);
    if(isset($terms)){
      return $taxonomyOBJ->leaguesFormater($terms,$lang);
    }
    else {
      $i = new  rollbarReport();
      $i->error("Sport/Tournament API ID '".$sportID."'  don't exist in this systems. Please check http://steveapi.rebelpenguin.dk:8000/api/competitions/'.$sportID.'");
      return null;
    }

  }

}
