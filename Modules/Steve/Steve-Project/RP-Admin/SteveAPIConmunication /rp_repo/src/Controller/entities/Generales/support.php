<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 2:01 PM
 */


namespace Drupal\rp_repo\Controller\entities\Generales;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class ImportAPIDATA.
 *
 * @package Drupal\rp_repo\Controller
 */
class support extends ControllerBase
{

  public function disableEvents()
  {
    set_time_limit(120);
    $date = date('Y-m-d');
    $fromDate = strtotime($date);
    $endDate = strtotime('+' . 1 . ' day', strtotime($date));
    $getInfoObj = new RepoGeneralGetInfo();
    $parametersList = $getInfoObj->getConfig($date, 1);
    $rpClient = RPAPIClient::getClient();
    $allSchedule = $rpClient->getschedule($parametersList[0]);


    //Enabling Events
    $Eventlist = [];
    foreach ($allSchedule as $event) {
      $node = $getInfoObj->getNode($event["id"], 'events', 'field_event_api_id');
      $Eventlist[] = $event["id"];
      if ($node) {
        $node = $node = Node::load(reset($node)->nid->value);
        $translations = $node->getTranslationLanguages(true);
        foreach ($translations as $key => $value) {
          if ($node->getTranslationStatus($key)) {
            $nodeTrasnlation = $node->getTranslation($key);
            $nodeTrasnlation->status = 1;
            $nodeTrasnlation->save();
          }
        }
      }
    }

    //Disabling Events
    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'events')
      ->condition('field_event_date', $fromDate, '>')
      ->condition('field_event_date', $endDate, '<')
      ->execute();
    foreach ($ids as $nid) {
      $node = Node::load($nid);
      $apiID = $node->field_event_api_id->value;
      if (!in_array($apiID, $Eventlist)) {
        $translations = $node->getTranslationLanguages(true);
        foreach ($translations as $key => $value) {
          if ($node->getTranslationStatus($key)) {
            $nodeTrasnlation = $node->getTranslation($key);
            $nodeTrasnlation->status = 0;
            $nodeTrasnlation->save();
          }
        }
      }
    }
    return true;
  }

  public function deleteEntityProcess($entity_type)
  {
    $query = \Drupal::entityQuery($entity_type);
    $ids = $query->execute();
    foreach ($ids as $nid) {
      $entity = \Drupal::entityTypeManager()
        ->getStorage($entity_type)
        ->load($nid);
      $entity->delete();
      echo "Delete Entity : " . $nid;
      echo "\n";
    }
    return true;
  }

  public function getClearUrl($s)
  {
    $s = trim($s, "\t\n\r\0\x0B");
    //--- Latin ---//
    $s = str_replace('ü', 'u', $s);
    $s = str_replace('Á', 'A', $s);
    $s = str_replace('á', 'a', $s);
    $s = str_replace('é', 'e', $s);
    $s = str_replace('É', 'E', $s);
    $s = str_replace('í', 'i', $s);
    $s = str_replace('Í', 'I', $s);
    $s = str_replace('ó', 'o', $s);
    $s = str_replace('Ó', 'O', $s);
    $s = str_replace('Ú', 'U', $s);
    $s = str_replace('ú', 'u', $s);
    //--- Nordick ---//
    $s = str_replace('ø', 'o', $s);
    $s = str_replace('Ø', 'O', $s);
    $s = str_replace('Æ', 'E', $s);
    $s = str_replace('æ', 'e', $s);
    $s = str_replace('Å', 'A', $s);
    $s = str_replace('å', 'a', $s);
    //--- Others ---//

    $s = str_replace(' - ', '-vs-', $s);
    $s = str_replace(' ', '_', $s);
    $s = str_replace('.', '_', $s);
    $s = str_replace('\"', '_', $s);
    $s = str_replace(':', '_', $s);
    $s = str_replace(',', '_', $s);
    $s = str_replace(';', '_', $s);
    $s = str_replace('/', '_', $s);
    $s = strtolower($s);
    $s = trim($s, "\t\n\r\0\x0B");
    return $s;
  }

  public function getMultiplesAlias($urldata)
  {
    $data = RPCmsSiteInfoHelper::getSiteInfoCombos();
    $slugify = new Slugify();
    foreach ($data as $siteInfo) {
      $alia = '/' . $this->getClearUrl($slugify->slugify($siteInfo[0])) . '/' . $slugify->slugify($urldata);
      $ifAlias = $this->getAliasbyUrl($alia);
      if (empty($ifAlias)) {
        $list [] = ['alias' => $alia];
      }
    }
    return $list;
  }

  public function getAliasbyUrl($alias)
  {
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $query_selct->condition('url.alias', $alias, '=');
    $data = $query_selct->execute()->fetchAll();
    return $data;
  }

  public function getAlias($nid, $alias)
  {
    $query_selct = \Drupal::database()->select('url_alias', 'url');
    $query_selct->fields('url', ['pid', 'alias', 'source']);
    $query_selct->condition('url.source', $nid, '=');
    $query_selct->condition('url.alias', $alias, '=');
    $data = $query_selct->execute()->fetchAll();
    return $data;
  }


}
