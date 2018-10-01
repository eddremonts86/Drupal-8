<?php

namespace Drupal\rp_api;

use Drupal\Component\Plugin\PluginBase;
use Drupal\rp_api\Utility\RPAPIHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rp_api\ChangedFields\RPAPIChangedFields;

/**
 * A base class to help developers implement their own RPAPIModel plugins.
 *
 * @see \Drupal\rp_api\Annotation\RPAPIModel
 * @see \Drupal\rp_api\RPAPIModelInterface
 */
abstract class RPAPIModelBase extends PluginBase implements RPAPIModelInterface, ContainerFactoryPluginInterface {

  /**
   * The rpApiHelper.
   *
   * @var RPAPIHelper
   */
  protected $rp_api;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $class = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('rp_api.helper')
    );
    return $class;
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RPAPIHelper $RPAPIHelper) {
    $this->rp_api = $RPAPIHelper;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

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

  /**
   * {@inheritdoc}
   */
  public function is_result_single() {
    return ($this->pluginDefinition['single']) ? TRUE : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function get_api_client() {
    return RPAPIClient::getClient();
  }

  /**
   * {@inheritdoc}
   */
  public function clean_global_wrapper($data) {
    $global_wrapper = $this->pluginDefinition['global_wrapper'];
    if(!empty($global_wrapper) && isset($data[$global_wrapper])) {
      return $data[$global_wrapper];
    }
    else {
      return $data;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function clean_item_wrapper($data) {
    $item_wrapper = $this->pluginDefinition['item_wrapper'];
    if(!empty($item_wrapper) && isset($data[$item_wrapper])) {
      return $data[$item_wrapper];
    }
    else {
      return $data;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function import($update = false, $query_vars = [], $debug = false) {
    $entity_name = $this->entity();
    $map = $this->mapping();

    $query_map_values = $this->queryMapValues($map, $query_vars);

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
      $entities_load = $entity->loadMultiple($ids);
    }

    $entity_create = [];
    //drush_print_r($map);
    foreach($models as $key => $model) {
      $model = $this->clean_item_wrapper($model);
      $model = $this->queryMapsAdd($model, $query_map_values);
      $data = $this->transformData($map, $model);

      // Update
      if(isset($ids[$model['id']]) && $update) {
        $entity_load = $entities_load[$ids[$model['id']]];
        $entity_new = $this->rp_api->entityCreate($this->entity(),$data, false);
        $entity_subject = new RPAPIChangedFields($entity_load, $entity_new, array_keys($map));
        $changed_fields = $entity_subject->checkEntityFields();
        if(!empty($changed_fields)) {
          $entity_create[] = $this->rp_api->entityUpdateWithChangedFields($entity_load, $changed_fields);
        }
      }
      // Create
      else if(!isset($ids[$model['id']])) {
        $entity_create[] = $this->rp_api->entityCreate($this->entity(),$data);
      }
    }

    return $entity_create;
  }

  /**
   * Get field api to field drupal relation
   */
  public function getFieldApiMapping() {
    $map = $this->mapping();

    $mapping = [];
    foreach($map as $field_name => $data) {
      $mapping[$data['api_field']] = $field_name;
    }
    return $mapping;
  }

  /**
   * Check if input parameter is a field api related with field drupal
   */
  public function checkFieldApiExists($field_api_name) {
    $map = $this->getFieldApiMapping();
    if(in_array($field_api_name,array_keys($map))) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Check if entity exists
   */
  public function checkEntityByApiIdExists($id, $entity_name = '') {
    if(empty($entity_name))
      $entity_name = $this->entity();

    $entity = \Drupal::entityManager()->getStorage($entity_name)->create();

    $ids = $this->rp_api->entityCheckExists($entity_name,[$entity->getFieldApiId() => $id]);

    if(!empty($ids)) {
      return $ids;
    }
    else {
      return false;
    }
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
      $entity_ids = $this->rp_api->entityCheckExists($entity_name,[$entity->getFieldApiId() => $id]);
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

  public function transformData($map, $model) {
    $data = [];
    $data['user_id'] = 1;
    foreach($map as $field => $field_def) {

      if(!isset($field_def['field_type']))
        $data[$field] =  $model[$field_def['api_field']];

      else if($field_def['field_type'] == 'datetime')
        $data[$field] =  substr($model[$field_def['api_field']],0,19);

      // Reference by reference_schema
      else if($field_def['field_type'] == 'entity_reference' && isset($field_def['reference_schema'])) {
        $data[$field] = [];
        $value = $model[$field_def['api_field']];
        if(gettype($value) == $field_def['reference_schema']['type']) {

          // It its not subtype
          if(!isset($field_def['reference_schema']['subtype'])) {

            // If its an array
            if($field_def['reference_schema']['type'] == 'array') {
              if(isset($value[$field_def['reference_schema']['key']])) {
                $data[$field] = $this->entityCheckByModelId($value[$field_def['reference_schema']['key']], $model, $field_def);
              }
            }

          }

          // It is a array subtype
          if(isset($field_def['reference_schema']['subtype']) && $field_def['reference_schema']['subtype'] == 'array') {

            // If its an array
            if($field_def['reference_schema']['type'] == 'array' && isset($value[0][$field_def['reference_schema']['key']])) {
              foreach($value as $k => $v) {
                $data[$field][] = current($this->entityCheckByModelId($v[$field_def['reference_schema']['key']], $model, $field_def));
              }
            }

          }
        }
      }

      // Reference by array
      else if($field_def['field_type'] == 'entity_reference' && is_array($model[$field_def['api_field']])) {
        $data[$field] = [];
        foreach($model[$field_def['api_field']] as $ref_id) {
          $ids = $this->rp_api->entityCheckExists($field_def['entity_type'],[$field_def['entity_field_reference'] => $ref_id ]);
          if($ids) {
            $data[$field][] = array_values($ids)[0];
          }
          else {
            drupal_set_message(
              t('Reference does not exist: %type (%ref - Model id %id)', [
                  '%type' => $field_def['entity_type'],
                  '%ref' => $field_def['entity_field_reference'],
                  '%id' => $model['id'],
              ])
              , 'error'
            );
          }
        }
      }

      // Reference by int
      else if($field_def['field_type'] == 'entity_reference' && !is_array($model[$field_def['api_field']])) {
        $data[$field] = [];
        $ref_id = $model[$field_def['api_field']];
        if(!empty($ref_id)) {
          $ids = $this->rp_api->entityCheckExists($field_def['entity_type'],[$field_def['entity_field_reference'] => $ref_id ]);
          if($ids) {
            $data[$field][] = array_values($ids)[0];
          }
          else {
            drupal_set_message(
              t('Reference does not exist: %type (%ref - Model id %id)', [
                '%type' => $field_def['entity_type'],
                '%ref' => $field_def['entity_field_reference'],
                '%id' => $model['id'],
              ])
              , 'error'
            );
          }
        }
      }

    }
    return $data;
  }

  private function entityCheckByModelId($ref_id, $model, $field_def) {
    $data = [];
    $ids = $this->rp_api->entityCheckExists($field_def['entity_type'],[$field_def['entity_field_reference'] => $ref_id ]);
    if($ids) {
      $data[] = array_values($ids)[0];
    }
    else {
      drupal_set_message(
        t('Reference does not exist: %type (%ref - Model Id: %id - Model Related Ref Id: %ref_id)', [
          '%type' => $field_def['entity_type'],
          '%ref' => $field_def['entity_field_reference'],
          '%id' => $model['id'],
          '%ref_id' => $ref_id,
        ])
        , 'error'
      );
    }
    return $data;
  }

  public function queryMapValues($map, $query_vars) {
    $values = [];

    foreach($map as $field => $field_def) {
      if(isset($field_def['source']) && $field_def['source'] == 'query_vars') {
        $values[$field_def['api_field']] = $query_vars[$field_def['api_field']];
      }
    }
    return $values;
  }

  private function queryMapsAdd($model, $query_map_values){
    if(empty($query_map_values))
      return $model;
    else {
      foreach($query_map_values as $key => $value) {
        $model[$key] = $value;
      }
    }
    return $model;
  }

}
