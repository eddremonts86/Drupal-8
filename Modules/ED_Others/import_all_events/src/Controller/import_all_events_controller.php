<?php

/**
 * Created by PhpStorm.
 * User: edd
 * Date: 30-04-17
 * Time: 14:44
 */

namespace Drupal\import_all_events\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

class import_all_events_controller extends ControllerBase {

  public function api_conect($url) {
    $client = curl_init($url);
    curl_setopt($client, CURLOPT_RETURNTRANSFER, TRUE);
    $respon = curl_exec($client);
    $result = (array) json_decode($respon);
    return $result;
  }
  public function getlocalcontenttype(){
    $taxonomy = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    foreach ($taxonomy as $ter) { $all_nodes[] = $ter->toArray();}
    $type = array();
    foreach ($all_nodes as $node) {
      $type[$node['type']] = $node['type'];
    }
    return $type;
  }
  public function getrevisions($url){
    $apiArray = $this->api_conect($url);
    $data = 'http://eddremonts.dk/themes/eddremonts/assets/images/portafolio/entradas/RebelPenguin.jpg';
    $image = file_get_contents($data);
    $file = file_save_data($image, 'public://articles_imgs/druplicon.png',FILE_EXISTS_REPLACE);


   $i=0;
   foreach ($apiArray['events'] as $event){
      $title = $event->away->name.' vs '.$event->home->name;
      $node = Node::create([
        'type'        => 'events_details',
        'title'       => $title,
        'body'       => $title,

      ]);
      $node->save();

      if($i ==10){break;} $i++;

    }
   }
}