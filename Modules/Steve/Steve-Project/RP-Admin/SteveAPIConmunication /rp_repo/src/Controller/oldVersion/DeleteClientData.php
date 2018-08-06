<?php

namespace Drupal\rp_repo\Controller\oldVersion;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\Controller\oldVersion\RepoGeneralGetInfo;

/**
 * Class DeleteClientData.
 */
class DeleteClientData extends ControllerBase
{

  /**
   * Deleteclientdata.
   *
   * @return string
   *   Return Hello string.
   */

  public function DeleteClientData()
  {
    $this->deleteNodeProccess();
    $this->deleteTaxonomyItems();
    return true;
  }

  public function deleteNodeProccess()
  {
    $query = \Drupal::entityQuery('node');
    $ids = $query->execute();
    foreach ($ids as $nid) {
      $node = NODE::load($nid);
      echo "Delete node type - (".$node->getType().")- '" . $node->getTitle() . "'";
      echo "\n";
      $node->delete();
    }
    return true;
  }

  public function deleteTaxonomyItems()
  {
    $query = \Drupal::entityQuery('taxonomy_term');
    $ids = $query->execute();
    foreach ($ids as $id) {
      $taxonomy = Term::load($id);
      if (!empty($taxonomy)) {
        echo "Delete Taxonomy type - (".$taxonomy->getVocabularyId().") - '" . $taxonomy->getName() . "'";
        echo "\n";
        $taxonomy->delete();
      }
    }
    return true;

  }

  public function deleteNodeByType($type)
  {
    $query = \Drupal::entityQuery('node')->condition('type', $type);
    $ids = $query->execute();
    foreach ($ids as $nid) {
      $node = NODE::load($nid);
      echo "Delete node type (".$type.")'" . $node->title() . "' :" . $nid;
      echo "\n";
      $node->delete();
    }
    return true;
  }

  public function deleteTaxonomyByVocabulary($voc)
  {
    $obj = ['vid' => $voc];
    $query = \Drupal::entityQuery('taxonomy_term')->loadByProperties($obj);
    $ids = $query->execute();
    foreach ($ids as $id) {
      $taxonomy = Term::load($id);
      if (!empty($taxonomy)) {
        $taxonomy->delete();
      }
      echo "Delete Taxonomy (".$voc."): " . $id;
      echo "\n";
    }
    return true;

  }

  public function deleteEntityProccess($entity_type)
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

  public function desableEvents()
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

    //Desabling Events

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
}
