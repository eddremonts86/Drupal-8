<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 8/10/18
 * Time: 3:56 PM
 */

namespace Drupal\rp_events_api\Controller\events;
use  Drupal\rp_events_api\Controller\api\EventsRepoService;
use Drupal\node\Entity\Node;
use Drupal\rp_repo\Controller\entities\Pages\nodeEntities;
use Drupal\rp_repo\Controller\entities\Generales\images;

class eventAPIImport {

    public function getAll($date , $page){
      $eventsAPI = EventsRepoService::getClient();
      $obj=array('date' => $date, 'page' => $page);
      $events = $eventsAPI->updateEvents($obj);
      if(isset($events)){
        $this->importEventNode($events['data']["events"]);
        if ($events['data']['next'] != 'null'){
          return $this->getAll($date , $events['data']['next']);
         }

      }else return TRUE;
    }

    public function geteventByID($id){
      $eventsAPI = EventsRepoService::getClient();
      $obj=array('eventid'=>$id);
      $events = $eventsAPI->updateEvent($obj);
      $updated = array();
      if(isset($events)){
        $updated[] =   $this->importEventNode($events['data']["events"]);
      }
      else {
        return false;
      }
      return $updated;
    }


    public function getAllTranslaion($date , $page,$lang){
      $eventsAPI = EventsRepoService::getClient();
      $obj=array('date' => $date, 'page' => $page, 'lang'=>$lang);
      $events = $eventsAPI->updateEventsTranslation($obj);
      if(isset($events)){
        $this->importEventTranslation($events['data']["events"],$lang);
        if ($events['data']['next'] != 'null'){
          return $this->getAllTranslaion($date , $events['data']['next'],$lang);
        }

      }
      else {return TRUE;}
      return TRUE;
    }

    public function geteventTranslaionByID($id,$lang){
      $eventsAPI = EventsRepoService::getClient();
      $obj=array('eventid'=>$id,'lang'=>$lang);
      $events = $eventsAPI->updateEventTranslation($obj);
      if(isset($events)){
        $this->importEventTranslation($events['data']["events"],$lang);
      }
      else return false;
    }



    public function getEventsRevisions($id){
      $eventsAPI = EventsRepoService::getClient();

    }


   public function importEventTranslation($nodeArray,$lang){
    if (isset($nodeArray)){
      foreach ($nodeArray as $n)
      {
        $nodeT = new nodeEntities();
        $nodeID =  $nodeT->getNodeByAPIID($n['eventAPIID'],'events');
        if($nodeID){
          drush_print("Updating Event  ". $n['title']."\n");
          $node = Node::load($nodeID);
          if ($node->hasTranslation($lang)){
            $translation = $node->getTranslation($lang);
            $translation->body = [
              'summary' =>$n['contents']['summary'],
              'value' => $n['contents']['value'],
              'format' => ($n['contents']['format'] != ' ' )? $n['contents']['format'] : 'full_html'
            ];
            $translation->title = $n['title'];
            $translation->status = $n['status'];
            $translation->save();
          }
          else {
            $translation = $node->addTranslation($lang);
            $translation->body = [
              'summary' =>$n['contents']['summary'].' - importer',
              'value' => $n['contents']['value'].' - importer',
              'format' => ($n['contents']['format'] != ' ' )? $n['contents']['format'] : 'full_html'];
            $translation->title = $n['title'].' - importer';
            $translation->status = $n['status'];
            $translation->save();
          }
        }
      }
      return TRUE;
    }
    else{
      drush_print("No events to update");
      return false;
    }
  }

   public function importEventNode($nodeArray){
      $title = '';
      foreach ($nodeArray as $n)
        {
          $nodeT = new nodeEntities();
          $img = new images();
          $nodeID =  $nodeT->getNodeByAPIID($n['eventAPIID'],'events');
          if($nodeID){
            drupal_set_message("Updating Event  ". $n['title']."\n");
            $node = Node::load($nodeID);
            $node->body = [
                          'summary' =>$n['contents']['summary'],
                          'value' => $n['contents']['value'],
                          'format' => ($n['contents']['format'] != ' ' )? $n['contents']['format'] : 'full_html'
                        ];
            $node->field_promoted_schedule_top = $n['scheduleTop'];
            $node->title = $n['title'];
            $node->status = $n['status'];
            if(!empty($n['eventBG']['url']) and !empty($n['eventBG']['title'])) {
              $imgArray = $img->getImg($n['eventBG']['url'],$n['eventBG']['title']);
              $node->field_events_bg = $imgArray;
            }
            $node->field_event_promotetolivestream = $n['promoteLivestreampage'];
            $node->path = array( 'pathauto' => 0);
            $node->save();
          foreach ($n["eventAlias"] as $alias){
              $allAlias =  $nodeT->getAlias('/node/'.$node->nid->value);
              $afa = array();
              foreach ($allAlias as $nodealias){$afa[] =   $nodealias->alias;}
              if(!in_array($alias['url'],$afa)){
                \Drupal::service('path.alias_storage')->save("/node/".$node->nid->value, $alias['url'], $alias['langcode']);
              }
           }
          }
          $title = $n['title'];
        }
        return  $title;
    }
}
