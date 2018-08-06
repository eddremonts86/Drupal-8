<?php

namespace Drupal\rp_repoapi;

use Drupal\Component\Plugin\PluginBase;
use Drupal\rp_api\Utility\RPAPIHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rp_api\ChangedFields\RPAPIChangedFields;
use Drupal\rp_api\RPAPIModelInterface;
use Drupal\rp_api\RPAPIModelBase;
use Drupal\rp_repoapi\RPRepoAPIClient;

/**
 * A base class to help developers implement their own RPAPIConfig plugins.
 *
 * @see \Drupal\rp_api\Annotation\RPAPIModel
 * @see \Drupal\rp_api\RPAPIModelInterface
 */
abstract class RPRepoAPIConfigBase extends RPAPIModelBase implements RPAPIModelInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function description() {
    // Retrieve the @description property from the annotation and return it.
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function entity() {
    return $this->pluginDefinition['entity'];
  }

  /**
   * {@inheritdoc}
   */
  public function api_method() {
    return $this->pluginDefinition['api_method'];
  }

  public function get_api_client() {
    return RPRepoAPIClient::getClient();
  }


  /**
   * {@inheritdoc}
   */
  public function import($update = false, $query_vars = [], $debug = false) {
    $entity_name = $this->entity();
    $map = $this->mapping();

    /** @var $entity */
    $entity = \Drupal::entityManager()->getStorage($entity_name);

    $rpClient = $this->get_api_client();
    $results = $rpClient->getMethod($this->api_method(), $query_vars, $debug);
    if($this->is_result_single())
      $models = [$results];
    else
      $models = $this->clean_global_wrapper($results);

    if($debug)
      drupal_set_message(print_r($models,TRUE));

    if(empty($models))
      return false;

    // Models load in API
    $model_ids = $rpClient->getModelIds($models);

    // Entities load in drupal by relation with model id on API
    $ids = $this->checkEntitiesByApiIdsExists($model_ids);

    // Load all entities from drupal
    if($ids) {
      $entities_load = $entity->loadMultiple(array_keys($ids));
    }

    $entity_create = [];

    foreach($models as $key => $model) {
      $model = $this->clean_item_wrapper($model);
      $data = $this->transformData($map, $model);

      // Update
      if(isset($ids[$model['id']]) && $update) {
        $entity_load = $entities_load[$model['id']];
        $entity_load->delete();
        $entity_create[] = $this->rp_api->entityCreate($this->entity(),$data);
      }
      // Create
      else if(!isset($ids[$model['id']])) {
        $entity_create[] = $this->rp_api->entityCreate($this->entity(),$data);
      }
    }

    return $entity_create;
  }

  /**
   * Check if entity exists
   */
  public function checkEntitiesByApiIdsExists($ids, $entity_name = '') {
    if(empty($entity_name))
      $entity_name = $this->entity();

    $entity = \Drupal::entityManager()->getStorage($entity_name)->create();

    $exist_ids = [];
    foreach($ids as $id) {
      $entity_ids = $this->rp_api->entityConfigCheckExists($entity_name,['name' => $id]);
      if($entity_ids) {
        foreach($entity_ids as $key => $entity_id)
          $exist_ids[$id] = $entity_id;
      }
    }

    if(!empty($exist_ids)) {
      return $exist_ids;
    }
    else {
      return false;
    }
  }


}
