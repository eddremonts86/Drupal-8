<?php

use Drupal\rp_api\RPAPIModelManager;
use Drupal\rp_api\RPAPIDescription;

/**
* Implements hook_drush_command().
*/
function rp_base_drush_command() {

 $commands['rp-base-delete-all-entities'] = [
   'description' => 'RP Base remove all content for entity type',
   'callback' => 'rp_base_drush_delete_all_entities',
   'aliases' => ['rpbasedae'],
   'arguments' => [
      'entity' => 'Entity',
    ],
    'examples' => [
      'drush rpbasedae site' => 'Delete all entities for "site" type',
    ],
 ];

 return $commands;
}

function rp_base_drush_delete_all_entities($entity){

  if(is_null($entity))
    return drush_set_error('DRUSH_FRAMEWORK_ERROR', dt('Entity is not defined'));

  if (drush_confirm('Are you sure you want delete all entities?')) {
    $ids = \Drupal::entityQuery($entity)->execute();
    $storage_handler = \Drupal::entityTypeManager()->getStorage($entity);
    $entities = $storage_handler->loadMultiple($ids);
    $storage_handler->delete($entities);

    drush_print(dt('Delete all entities for @entity type: @ids', ['@entity' => $entity, '@ids' => implode(', ',$ids)]));
  }
  else {
    drush_user_abort();
  }
}