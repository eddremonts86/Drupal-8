<?php

namespace Drupal\rp_cms_theme_interaction;
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 6/28/17
 * Time: 11:57 AM
 */
class node_tree_creator {
  public function tree_creator($data) {
    $tree = [];
    foreach ($data as $event) {
      $date = date("Y-m-d", $event['field_game_date'][0]['value']);
      $leage = $event['field_game_tournament'][0]['value'];
      $tournament_id = $event['field_game_tournament_api_id'][0]['value'];

      if (!$tree['AllEvents'][$date]) {
        $tree['AllEvents'][$date] = array();
        $tree['AllEvents'][$date][$leage]['events']= array();
        $tree['AllEvents'][$date][$leage]['tournamet']= $leage;
        $tree['AllEvents'][$date][$leage]['tournament_id']= $tournament_id;
        array_push($tree['AllEvents'][$date][$leage]['events'],$event);
      }
      else if($tree['AllEvents'][$date]){
        if(!$tree['AllEvents'][$date][$leage] and $tree['AllEvents'][$date][$leage]['tournament_id'] != $tournament_id ){
           $tree['AllEvents'][$date][$leage]['tournamet']= $leage;
           $tree['AllEvents'][$date][$leage]['tournament_id']= $tournament_id;
          $tree['AllEvents'][$date][$leage]['events']=array();
          array_push($tree['AllEvents'][$date][$leage]['events'],$event);
        }
        else{
          array_push($tree['AllEvents'][$date][$leage]['events'],$event);
        }

      }
    }
   return $tree;


  }
  public function gettree($data,  $format) {
    $tree = [];
    foreach ($data as $event) {
      $date = date($format, $event['field_game_date'][0]['value']);
      $leage = $event['field_game_tournament'][0]['value'];
      $tournament_id = $event['field_game_tournament_api_id'][0]['value'];
      if (!$tree['AllEvents'][$date]) {
        $tree['AllEvents'][$date] = array();
        $tree['AllEvents'][$date][$leage]['events']= array();
        $tree['AllEvents'][$date][$leage]['tournamet']= $leage;
        $tree['AllEvents'][$date][$leage]['tournament_id']= $tournament_id;
        array_push($tree['AllEvents'][$date][$leage]['events'],$event);
      }
      else if($tree['AllEvents'][$date]){
        if(!$tree['AllEvents'][$date][$leage] and $tree['AllEvents'][$date][$leage]['tournament_id'] != $tournament_id ){
          $tree['AllEvents'][$date][$leage]['tournamet']= $leage;
          $tree['AllEvents'][$date][$leage]['tournament_id']= $tournament_id;
          $tree['AllEvents'][$date][$leage]['events']=array();
          array_push($tree['AllEvents'][$date][$leage]['events'],$event);
        }
        else{
          array_push($tree['AllEvents'][$date][$leage]['events'],$event);
        }

      }
    }
   return $tree;
  }
}