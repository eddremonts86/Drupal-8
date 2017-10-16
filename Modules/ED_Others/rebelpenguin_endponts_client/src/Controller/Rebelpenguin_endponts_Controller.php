<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/17/17
 * Time: 12:29 PM
 */

namespace Drupal\rebelpenguin_endponts_client\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
//use Drupal\file\Entity\File;

class rebelpenguin_endponts_Controller extends ControllerBase {


  public function getUrls() {

    $query_selct = \Drupal::database()
      ->select('rebel_endpoinsclients_urls', 're');
    $query_selct->fields('re', array('urlid', 'type', 'url'));
    $data = $query_selct->execute()->fetchAll();
    $array_ = array();
    for ($i = 0; $i < count($data); $i++) {
      $array_[$data[$i]->type] = $data[$i]->url;
    }
    return $array_;
  }
  public function lookforurls($type){
   $list = $this->getUrls();
   return $list[$type];

  }
  public function api_conect($url) {
    $client = curl_init($url);
    curl_setopt($client, CURLOPT_RETURNTRANSFER, TRUE);
    $respon = curl_exec($client);
    $result = (array) json_decode($respon);
    return $result;
  }
  public function saveUrls($values) {
    $endpoints_ = array(
      'node',
      'onerevisions',
      'allnodes',
      'revisions',
      'allalias',
      'aliasbyid',
      'alluser',
      'alltaxonomy',
      'allctypes'
    );

    for ($i = 0; $i < count($endpoints_); $i++) {
      $name = $endpoints_[$i];
      if ($values[$name] != '' and $values[$name] != NULL) {
        $query_selct = \Drupal::database()
          ->select('rebel_endpoinsclients_urls', 're');
        $query_selct->fields('re', array('urlid'));
        $query_selct->condition('re.type', $name, '=');
        $data = $query_selct->execute()->fetchField();

        if ($data) {
          $query = \Drupal::database()->update('rebel_endpoinsclients_urls');
          $query->fields(['url' => $values[$name]]);
          $query->condition('type', $name);
          $query->execute();
          $message = $this->t('Update of ' . $name . ' URL, with value: ' . $values[$name] . '. Successes');
          drupal_set_message($message);
        }
        else {
          $fields = array('type' => $name, 'url' => $values[$name]);
          $nid = \Drupal::database()->insert('rebel_endpoinsclients_urls')
            ->fields($fields)
            ->execute();
          if ($nid) {
            $message = $this->t('Instertion of ' . $name . ' URL, with value: ' . $values[$name] . '. Successes');
            drupal_set_message($message);
          }
          else {
            $message = $this->t('Instertion of ' . $name . ' URL, with value: ' . $values[$name] . '. Failed');
            drupal_set_message($message);
          }
        }
      }


    }
  }
  public function nodes_import($apiArray) {
    $node = $apiArray['data']->nodes[0];
    $nid = $node->nid[0]->value;
    $uuid = $node->uuid[0]->value;
    $vid = $node->vid[0]->value;
    $langcode = $node->langcode[0]->value;
    $type = $node->type[0]->target_id;
    $status = $node->status[0]->value;
    $title = $node->title[0]->value;
    $uid = $node->uid[0]->target_id;
    $created = $node->created[0]->value;
    $changed = $node->changed[0]->value;
    $promote = $node->promote[0]->value;
    $sticky = $node->sticky[0]->value;
    $revision_timestamp = $node->revision_timestamp[0]->value;
    $revision_uid = $node->revision_uid[0]->target_id;
    $revision_translation_affected = $node->revision_translation_affected[0]->value;
    $default_langcode = $node->default_langcode[0]->value;
    $body = $node->body[0]->value;
    $b_summary = $node->body[0]->summary;
    $b_format = $node->body[0]->format;
    $img_target_id = $node->field_image[0]->target_id;
    $img_alt = $node->field_image[0]->alt;
    $img_title = $node->field_image[0]->title;
    $img_width = $node->field_image[0]->width;
    $img_height = $node->field_image[0]->height;
    $field_tags = $node->field_tags[0]->target_id;
    entity_delete_multiple('node', array($nid));
    $data = entity_create('node', array(
      'type' => $type,
      'nid' => $nid,
      'vid' => $vid,
      'uuid' => $uuid,
      'uid' => $uid,
      'status' => $status,
      'title' => $title,
      'langcode' => $langcode,
      'body' => array(
        'value' => $body,
        'summary' => $b_summary,
        'format' => $b_format,
      ),
      'created' => $created,
      'changed' => $changed,
      'promote' => $promote,
      'sticky' => $sticky,
      'revision_timestamp' => $revision_timestamp,
      'revision_uid' => $revision_uid,
      'revision_translation_affected' => $revision_translation_affected,
      'default_langcode' => $default_langcode,
      'field_image' => array(
        'alt' => $img_alt,
        'target_id' => $img_target_id,
        'title' => $img_title,
        'width' => $img_width,
        'height' => $img_height,
      ),
      'field_tags' => $field_tags
    ))->save();
    if($data){
      $message = $this->t('Node has been saved.');
    }

  }
  public function importmultiplenodes($node) {

    $nid = $node->nid[0]->value;
    $uuid = $node->uuid[0]->value;
    $vid = $node->vid[0]->value;
    $langcode = $node->langcode[0]->value;
    $type = $node->type[0]->target_id;
    $status = $node->status[0]->value;
    $title = $node->title[0]->value;
    $uid = $node->uid[0]->target_id;
    $created = $node->created[0]->value;
    $changed = $node->changed[0]->value;
    $promote = $node->promote[0]->value;
    $sticky = $node->sticky[0]->value;
    $revision_timestamp = $node->revision_timestamp[0]->value;
    $revision_uid = $node->revision_uid[0]->target_id;
    $revision_translation_affected = $node->revision_translation_affected[0]->value;
    $default_langcode = $node->default_langcode[0]->value;
    $body = $node->body[0]->value;
    $b_summary = $node->body[0]->summary;
    $b_format = $node->body[0]->format;
    $img_target_id = $node->field_image[0]->target_id;
    $img_alt = $node->field_image[0]->alt;
    $img_title = $node->field_image[0]->title;
    $img_width = $node->field_image[0]->width;
    $img_height = $node->field_image[0]->height;
    $field_tags = $node->field_tags[0]->target_id;
    entity_delete_multiple('node', array($nid));
    $data = entity_create('node', array(
      'type' => $type,
      'nid' => $nid,
      'vid' => $vid,
      'uuid' => $uuid,
      'uid' => $uid,
      'status' => $status,
      'title' => $title,
      'langcode' => $langcode,
      'body' => array(
        'value' => $body,
        'summary' => $b_summary,
        'format' => $b_format,
      ),
      'created' => $created,
      'changed' => $changed,
      'promote' => $promote,
      'sticky' => $sticky,
      'revision_timestamp' => $revision_timestamp,
      'revision_uid' => $revision_uid,
      'revision_translation_affected' => $revision_translation_affected,
      'default_langcode' => $default_langcode,
      'field_image' => array(
        'alt' => $img_alt,
        'target_id' => $img_target_id,
        'title' => $img_title,
        'width' => $img_width,
        'height' => $img_height,
      ),
      'field_tags' => $field_tags
    ))->save();
    if($data){
      $message = $this->t('Node has been saved.');
    }

  }
  public function getrevisions($node , $typeof){
    if($typeof == 'one'){ $preurle = $this->lookforurls('onerevisions');}
    else{$preurle = $this->lookforurls('revisions');}
    $url = $preurle . $node;
    $apiArray = $this->api_conect($url);
    $revisions = $apiArray['data']->revision;
    $count= 0;
    foreach ($revisions as $revision){
      $node_ = Node::load($node);
      $node_->nid =  $revision->nid[0]->value;
      $node_->uuid =  $revision->uuid[0]->value;
      $node_->revision_id = $revision->vid[0]->value;
      $node_->langcode = $revision->langcode[0]->value;
      $node_->type = $revision->type[0]->target_id;
      $node_->status = $revision->status[0]->value;
      $node_->title = $revision->title[0]->value;
      $node_->uid = $revision->uid[0]->target_id;
      $node_->created = $revision->created[0]->value;
      $node_->changed = $revision->changed[0]->value;
      $node_->promote = $revision->promote[0]->value;
      $node_->sticky = $revision->sticky[0]->value;
      $node_->revision_timestamp = $revision->revision_timestamp[0]->value;
      $node_->revision_uid = $revision->revision_uid[0]->target_id;
      $node_->revision_log = $revision->revision_log[0]->value;
      $node_->revision_translation_affected = $revision->revision_translation_affected[0]->value;
      $node_->default_langcode = $revision->default_langcode[0]->value;
      $node_->path = $revision->path[0]->value;
      $node_->menu_link = $revision->menu_link[0]->value;
      $node_->body_value = $revision->body[0]->value;
      $node_->body_summary = $revision->body[0]->summary;
      $node_->body_format = $revision->body[0]->format;
      $node_->field_image_target_id = $revision->field_image[0]->target_id;
      $node_->field_image_alt = $revision->field_image[0]->alt;
      $node_->field_image_title = $revision->field_image[0]->title;
      $node_->field_image_width = $revision->field_image[0]->width;
      $node_->field_image_height = $revision->field_image[0]->height;
      $node_->new_revision = TRUE;
      $node_->setNewRevision();
      $node_->save();

    }
  }
  public function getcontenttype(){
    $allctypes = $this->lookforurls('allctypes');
    $data = $this->api_conect($allctypes);
    return $data;
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
  public function importnodesbytype($revision){
    $url = 'http://dominios.br:81/rpendp/getNodesbytype.json?type='.$revision.'&page=0';
    $content = $this->api_conect($url);
    $total = 0;
    foreach ($content['data']->nodes as $node){
      $nid = $node->nid[0]->value;
      $this->importmultiplenodes($node);
      $total++;
    }
    return $total;

  }

}
