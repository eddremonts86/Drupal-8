<?php

namespace Drupal\rp_api\Utility;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class RPAPIHelper  {

  /**
   * The entity manger.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityManager;

  /**
   * Constructs a new BlockRepository.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_manager')
    );
  }

  /**
   * Create entity
   */
  public function entityCreate($entityType, array $values, $save = true) {
    $storage = $this->entityManager->getStorage($entityType);
    $entity =  $storage->create($values);
    if($save)
      $entity->save();
    return $entity;
  }

  /**
   * Update entity
   */
  public function entityUpdate($entity, array $values) {
    foreach($values as $field_name => $field_value) {
      $entity->set($field_name, $field_value);
    }
    $entity->save();
    return $entity;
  }

  /**
   * Update entity from changed fields API
   */
  public function entityUpdateWithChangedFields($entity, array $changed_fields, $save = true) {
    foreach($changed_fields as $field_name => $field_data) {
      $entity->$field_name = $field_data['new_value'];
    }
    if($save)
      $entity->save();
    return $entity;
  }


  /**
   * Check entity
   */
  public function entityCheckExists($entityType, array $values) {

    $query = \Drupal::service('entity.query')->get($entityType);

    foreach($values as $field_name => $field_value) {
      $query->condition($field_name, $field_value);
    };

    $ids = $query->execute();

    if (empty($ids)) {
      return false;
    }
    else {
      return $ids;
    }

  }

  /**
   * Create entity
   */
  public static function setFieldName($name) {
    if(substr($name,0,6) != 'field_')
      return 'field_'.$name;
    else {
      return $name;
    }
  }


}
