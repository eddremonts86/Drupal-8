<?php

namespace Drupal\rp_metadata;

class MetadataFieldClient {

  public static function load() {
    return \Drupal::config('rp_metadada.field_hierarchy');
  }

  public function getTrackByEntityAndField($entity_name, $field_name) {
    $config = \Drupal::config('rp_metadada.field_hierarchy');
    $tracks = $config->get('tracks');
    foreach($tracks as $track => $data) {
      if(isset($data[$entity_name])) {
        if(isset($data[$entity_name]['field']) && $data[$entity_name]['field'] == $field_name) {
          return $tracks[$track];
        }
      }
    }
    return false;
  }

  public function getHierarchicalByEntityAndField($entity_name, $field_name) {
    $family = [];
    $track = $this->getTrackByEntityAndField($entity_name, $field_name);
    if(!empty($track)) {
      foreach($track as $entity => $data) {
        if($entity == $entity_name && $data['field'] == $field_name){
          if(isset($data['parent'])) {
            $family[$entity_name] = $data;
            return $this->getFieldHierarchy($family, $track, $data['parent']);
          }

        }
      }
    }
    return false;

  }

  public function getFieldHierarchy($family, $track, $parent) {
    if(isset($track[$parent])) {
      if(isset($track[$parent]['parent'])) {
        $family[$parent] = $track[$parent];
        return $this->getFieldHierarchy($family, $track, $track[$parent]['parent']);
      }
      else {
        $family[$parent] = $track[$parent];
        return $family;
      }

    }
    return false;
  }

  public function getFieldValue($family, $entity_name, $entity) {

    if(isset($family[$entity_name])) {
      $data = $family[$entity_name];
      $field_name = $data['field'];
      $parent_reference = $data['parent_reference'];
      $entity_up = false;
      if(isset($data['relative']['parent'])) {
        $entity_relative = $entity->get($data['relative']['reference'])->entity;
        $parent_reference = $data['relative']['parent_reference'];
        $entity_reference = $entity_relative->get($parent_reference)->entity;
        $entities = $this->getNephew(
          $entity_reference->bundle(),
          $entity_reference->id(),
          $entity->get('field_'.$entity_name.'_language')->entity->id(),
          $entity->get('field_'.$entity_name.'_region')->entity->id(),
          $entity->get('field_'.$entity_name.'_site')->entity->id()
        );
        if($entities) {
          $entity_up = current($entities);
          $entity_up_name = $entity_up->bundle();
          $field_name_up = $family[$entity_up_name]['field'];
        }
      }
      else {
        $entity_up = $entity->get($parent_reference)->entity;
        $entity_up_name = $entity_up->bundle();
        $field_name_up = $family[$entity_up_name]['field'];
      }

      if(!empty($entity_up ) && !empty($entity_up->get($field_name_up)->value)){
        return $entity_up->get($field_name_up)->value;
      }
      elseif(isset($family[$entity_up_name]['parent'])) {
        return $this->getFieldValue($family,$entity_up_name,$entity_up);
      }
      else {
        return;
      }
    }


  }

  public function getNephew($entity_name, $entity_uncle, $language, $region, $site) {
    $field_language = 'field_'.$entity_name.'_info_language';
    $field_region = 'field_'.$entity_name.'_info_region';
    $field_site = 'field_'.$entity_name.'_info_site';
    $field_ref = 'field_'.$entity_name.'_info_'.$entity_name;
    $query = \Drupal::entityQuery($entity_name.'_info')
                    ->condition('status', 1)
                    ->condition($field_ref, $entity_uncle)
                    ->condition($field_language, $language)
                    ->condition($field_region, $region)
                    ->condition($field_site, $site);

    $ids = $query->execute();
    if(!empty($ids)) {
      $storage = \Drupal::entityManager()->getStorage($entity_name.'_info');
      return $storage->loadMultiple($ids);
    }
    else {
      return false;
    }
  }



}