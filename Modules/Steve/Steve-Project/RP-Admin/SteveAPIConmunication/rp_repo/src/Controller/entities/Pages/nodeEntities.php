<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 8/10/18
 * Time: 4:53 PM
 */

namespace Drupal\rp_repo\Controller\entities\Pages;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;
use Drupal\rp_repo\Controller\entities\Taxonomies\taxonomyTournament;
use Drupal\rp_repo\Controller\entities\Generales\images;


class nodeEntities {
  public function getNodes($page,$date,$nodeID,$nodeType,$lang = ''){
    $nids = \Drupal::entityQuery('node')->condition('type', $nodeType);
      if(isset($date)){
        $fromDate = strtotime($date);
        $endDate = strtotime('+' . 1 . ' day', strtotime($date));
        $nids ->condition('field_event_date', $fromDate, '>')
          ->condition('field_event_date', $endDate, '<');
      }
      if(isset($nodeID)){
        $nids ->condition('field_event_api_id', $nodeID, '=');
      }

      if(isset($lang) and !empty($lang)){
        $nids ->condition('langcode', $lang);
      }
    $nids = $nids->execute();
    $listpage = $this->paginations($nids, $page);
    $Eventsnodes = $this->getScheduleFormatExport($listpage['nodes'],$lang);
    $endarray = [
          'events' =>$Eventsnodes,
          'next' => $listpage['next']
    ];
    return $endarray;

  }
  public function getNodeReviw($nodeID,$type){
    $nids = \Drupal::entityQuery('node')
        ->condition('type', $type)
        ->condition('field_event_api_id', $nodeID, '=')->execute();
    foreach ($nids as $nid)
    {
      $nids = $nid;
      break;
    }

    $endarray = [
      'eventsRevision' =>$this->getNoderevisions($nids),
    ];
    return $endarray;

  }
  public function getNode($ids)
  {
    $all_nodes = [];
      foreach ($ids as $id) {
        $all_nodes = Node::load($id);
      }
    return $all_nodes;
  }
  public function getNodeByAPIID($nodeAPIID,$type){
    $nids = \Drupal::entityQuery('node')->condition('type', $type);
    $nids ->condition('field_event_api_id', $nodeAPIID, '=');
    $nids = $nids->execute();
    if(!empty($nids) and isset($nids)){
      foreach ($nids as $nid){
        return  $nid;
      }
    }
    else return false;
  }
  public function paginations($nids, $page = 0,$items = 100)
  { $pageList = array_chunk($nids, $items);
    $result = array();
    if (!isset($page) || $page == 0|| $page == NULL) {
      $result = [
        'nodes' => $pageList[$page],
        'next' => isset($pageList[$page+1]) ? $page+1 : 'null',
      ];
      return $result;

    }else {
      $result = [
        'nodes' => $pageList[$page],
        'next' => isset($pageList[$page+1]) ? $page+1 : 'null',
      ];
      return $result;
    }
  }
  public function getScheduleFormatExport($nodeList,$lang)
  {
    if(!isset($lang))
    {$newNodeList =  array();
      if(isset($nodeList)){
        foreach ($nodeList as $nodeId) {
          $simpleNode = Node::load($nodeId);
          $newNodeList[] = $this->format($simpleNode);
          }
      }
     return $newNodeList;
    }
    else {
      return $this->getNodeTranslation($nodeList,$lang);
    }
  }
  public function getNodeAlias($id, $absolute = FALSE)
  {
    $options = [];
    if ($absolute) {
      $options['absolute'] = TRUE;
    }
    $url = Url::fromRoute('entity.node.canonical', ['node' => $id], $options)->toString();
    return $url;
  }
  public function getNoderevisions($id){
    $node[]= $id;
    $vids = \Drupal::entityManager()->getStorage('node')->revisionIds(Node::load($id));

    foreach ($vids as $vid){
        $rev = \Drupal::entityManager()->getStorage('node')->loadRevision($vid);
        $revisions[] =  $this->format($rev);
       }
     return $revisions;
    }
  public function format($simpleNode){
      $img = new images();
      $taxonomies = new taxonomyTournament();
       $pro=array();
      foreach ($simpleNode->field_event_promotetolivestream as  $promote){
          $term = Term::load($promote->target_id);
          $pro[] = [
              'name'=> $term->getName(),
              'apiID' => isset($term->field_api_id->value) ? $term->field_api_id->value : $term->field_api_id->value
          ];
      }
    $aliasLis = $this->getAlias('/node/'.$simpleNode->nid->value);
    $alias=array();
    foreach ($aliasLis as $alia){
        $alias[] = array(
                     'url'=> $alia->alias,
                     'langcode'=> $alia->langcode
                );
          }

    $sportOBJ = [
          'vid'=>'sport',
          'tid'=>$simpleNode->field_events_sport->target_id
        ];
    $tournamentOBJ =[
          'vid'=>'sport',
          'tid'=>$simpleNode->field_event_tournament->target_id
        ];
    $imageURL=  $img->getImgUrl_toexport($simpleNode->field_events_bg->target_id);
    $newNodeList = [
        'CRIDNode' => $simpleNode->nid->value,
        'sportAPIID'=>reset($taxonomies->getTaxonomyByOBj($sportOBJ,'obj'))->field_api_id->value,
        'tournamentAPIID'=>reset($taxonomies->getTaxonomyByOBj($tournamentOBJ,'obj'))->field_api_id->value,
        'eventAPIID' => $simpleNode->field_event_api_id->value,
        'status' => $simpleNode->status->value,
        'title' => $simpleNode->title->value,
        'eventAlias' => $alias,
        'promoteLivestreampage'=>$pro ,
        'scheduleTop'=> $simpleNode->field_promoted_schedule_top->value,
        'contents'=> [
                  'summary' => $simpleNode->body->summary,
                  'value' => $simpleNode->body->value,
                  'format' => $simpleNode->body->format == '' ? 'full_html' : $simpleNode->body->format,
              ],
        'eventBG'=> [
          'alt' => $simpleNode->field_events_bg->alt,
          'title' => empty($simpleNode->field_events_bg->title) ? $simpleNode->field_events_bg->alt : $simpleNode->field_events_bg->title,
          'url' => (isset($imageURL) and !empty($imageURL ))? $imageURL : null
        ],

      ];
      return $newNodeList;
    }
  public function getAlias($nid) {
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source','langcode']);
    $query_selct->condition('url.source', $nid, '=');
    $data = $query_selct->execute()->fetchAll();
    return $data;
  }
  public function getNodeTranslation($nodeid,$langCode){
    if(!isset($language) or $language == '' or empty($language)){
      $language = \Drupal::languageManager()->getDefaultLanguage()->getId();
    }
    else{
      $language = \Drupal::languageManager()->getLanguage($langCode)->getId();
    }
   $elements = array();
   if ($language){
     if(isset($nodeid)){
      foreach ($nodeid as $id) {
      $node = Node::load($id);
      if ($node->hasTranslation($language)){
      $translation = $node->getTranslation($language);
      if(!empty($translation) and isset($translation)){
          $elements[] = [
            'eventAPIID' => $translation->field_event_api_id->value,
            'langCode' => $language,
            'title' => $translation->title->value,
            'status' => $translation->status->value,
            'contents'=> [
              'summary' => $translation->body->summary,
              'value' => $translation->body->value,
              'format' => $translation->body->format == '' ? 'full_html' : $translation->body->format,
              ]
            ];
          }
        }
      }
       return $elements;
    }
    }
    }

}
