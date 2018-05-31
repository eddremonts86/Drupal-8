<?php

/**
 * @file
 * Contains NodeSubject.php.
 */

namespace Drupal\rp_api\ChangedFields;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Class EntitySubject.
 */
class RPAPIChangedFields  {

  /**
   * @var EntityInterface
   */
  private $entity;

  /**
   * @var EntityInterface
   */
  private $entity_new;

  /**
   * @var array
   */
  private $changed_fields;

  /**
   * @var array
   */
  private $info;

  /**
   * @param EntityInterface $entity
   * @param array $info
   */
  public function __construct(EntityInterface $entity, EntityInterface $entity_new, array $info) {
    $this->entity = $entity;
    $this->entity_new = $entity_new;
    $this->info = $info;
    $this->changedFields = array();
  }



  /**
   * {@inheritdoc}
   */
  public function checkEntityFields() {

    $changed_fields = [];
    foreach ($this->entity->getFieldDefinitions() as $field_name => $field_definition) {

      if(in_array($field_name,$this->info)) {
        $oldValue = $this->entity->get($field_name)->getValue();
        $newValue = $this->entity_new->get($field_name)->getValue();
        $result = $this->compareFieldValues($field_definition, $oldValue, $newValue);

        if (is_array($result) && !empty($result)) {
          $changed_fields[$field_name] = $result;
        }
      }

    }

    if (!empty($changed_fields)) {
      $this->changed_fields = $changed_fields;
    }
    return $changed_fields;

  }

  public function getChangedFieldsPairs() {
    $changed_fields = $this->getChangedFields();
    $changed_pairs = [];
    if (is_array($changed_fields) && !empty($changed_fields)) {
      foreach($changed_fields as $field_name => $values){
        $changed_pairs[$field_name] = $values['new_value'];
      }
    }
    return $changed_pairs;
  }


  /**
   * Method that returns comparable properties for existing field type.
   *
   * @param FieldDefinitionInterface $fieldDefinition
   * @return array
   */
  private function getComparableProperties(FieldDefinitionInterface $fieldDefinition) {
    switch ($fieldDefinition->getType()) {
      case 'string':
      case 'string_long':
      case 'text':
      case 'text_long':
      case 'boolean':
      case 'integer':
      case 'float':
      case 'decimal':
      case 'datetime':
      case 'email':
      case 'list_integer':
      case 'list_float':
      case 'list_string':
      case 'telephone':
        $properties = array('value');
        break;

      case 'text_with_summary':
        $properties = array(
          'value',
          'summary',
        );
        break;

      case 'entity_reference':
        $properties = array('target_id');
        break;

      case 'file':
        $properties = array(
          'target_id',
          'description',
        );
        break;

      case 'image':
        $properties = array(
          'fid',
          'width',
          'height',
          'target_id',
          'alt',
          'title'
        );
        break;

      case 'link':
        $properties = array(
          'uri',
          'title',
        );
        break;

      default:
        $properties = $this->getDefaultComparableProperties($fieldDefinition);
        break;
    }

    return $properties;
  }

  /**
   * Method that returns comparable properties for extra or custom field type.
   *
   * Use it if you want to add comparison support
   * for extra or custom field types.
   *
   * @param FieldDefinitionInterface $fieldDefinition
   * @return array
   */
  protected function getDefaultComparableProperties(FieldDefinitionInterface $fieldDefinition) {
    return array();
  }

  /**
   * Method that compares old and new field values.
   *
   * @param FieldDefinitionInterface $fieldDefinition
   * @param array $oldValue
   * @param array $newValue
   * @return array|bool
   */
  public function compareFieldValues(FieldDefinitionInterface $fieldDefinition, array $oldValue, array $newValue) {
    $result = TRUE;
    $properties = $this->getComparableProperties($fieldDefinition);

    // If value was added or removed then we have already different values.
    if ((!$oldValue && $newValue) || ($oldValue && !$newValue)) {
      $result = $this->makeResultArray($oldValue, $newValue);
    }
    else {
      if ($oldValue && $newValue) {
        // If value was added|removed to|from multi-value field then we have
        // already different values.
        if (count($newValue) != count($oldValue)) {
          $result = $this->makeResultArray($oldValue, $newValue);
        }
        else {
          // Walk through each field value and compare it's properties.
          foreach ($newValue as $key => $value) {
            if (is_array($result)) {
              break;
            }

            foreach ($properties as $property) {
              if ($newValue[$key][$property] != $oldValue[$key][$property]) {
                $result = $this->makeResultArray($oldValue, $newValue);
                break;
              }
            }
          }
        }
      }
    }

    return $result;
  }

  /**
   * Method that generates result array for DefaultFieldComparator::compareFieldValues().
   *
   * @param array $oldValue
   * @param array $newValue
   * @return array
   */
  private function makeResultArray(array $oldValue, array $newValue) {
    return array(
      'old_value' => $oldValue,
      'new_value' => $newValue,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityNew() {
    return $this->entity_new;
  }


  /**
   * {@inheritdoc}
   */
  public function getChangedFields() {
    return $this->changed_fields;
  }

}
