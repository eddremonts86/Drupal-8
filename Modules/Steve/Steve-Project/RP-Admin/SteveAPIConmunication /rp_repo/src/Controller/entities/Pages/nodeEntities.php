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

use Drupal\rp_repo\Controller\entities\Generales\images;


class nodeEntities {

  public function getNodes($page,$date,$nodeID,$type){
    $nids = \Drupal::entityQuery('node')->condition('type', $type);
      if(isset($date)){
        $fromDate = strtotime($date);
        $endDate = strtotime('+' . 1 . ' day', strtotime($date));
        $nids ->condition('field_event_date', $fromDate, '>')
          ->condition('field_event_date', $endDate, '<');
      }
      if(isset($nodeID)){
        $nids ->condition('field_event_api_id', $nodeID, '=');
      }
    $nids = $nids->execute();
    $listpage = $this->paginations($nids, $page);
    $Eventsnodes = $this->getScheduleFormatExport($listpage['nodes']);
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
  public function getScheduleFormatExport($nodeList)
  {
    $newNodeList =  array();
    if(isset($nodeList)){
      foreach ($nodeList as $nodeId) {
        $simpleNode = Node::load($nodeId);
        $newNodeList[] = $this->format($simpleNode);
        }
    }
    return $newNodeList;
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
  public function  format($simpleNode){
      $img = new images();
       $pro=array();
      foreach ($simpleNode->field_event_promotetolivestream as  $promote){
          $term = Term::load($promote->target_id);
          $pro[] = [
              'name'=> $term->getName(),
              'apiID' => isset($term->field_api_id->value) ? $term->field_api_id->value : $term->field_channel_api_id->value
          ];
      }

    $aliasLis = $this->getAlias('/node/'.$simpleNode->nid->value);
    $alias=array();
    foreach ($aliasLis as $alia){
          $alias[] = $alia->alias;
        }
    $newNodeList = [
        'status' => $simpleNode->status->value,
        'title' => $simpleNode->title->value,
        'eventAlias' => $alias,
        'nidAPI' => $simpleNode->field_event_api_id->value,
        'body' => $simpleNode->body->value,
        'promoteLivestreampage'=>$pro ,
        'scheduleTop'=> $simpleNode->field_promoted_schedule_top->value,
        'eventBG'=> [
          'alt' => $simpleNode->field_events_bg->alt,
          'title' => ($simpleNode->field_events_bg->title == '') ? $simpleNode->field_events_bg->alt : $simpleNode->field_events_bg->title,
          'url' =>$img->getImgUrl_toexport($simpleNode->field_events_bg->target_id)
        ]
      ];
      return $newNodeList;
    }
  public function getAlias($nid) {
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $query_selct->condition('url.source', $nid, '=');
    $data = $query_selct->execute()->fetchAll();
    return $data;
  }


}
