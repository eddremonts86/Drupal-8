<?php

use Drupal\rp_api\RPAPIModelManager;
use Drupal\rp_api\RPAPIDescription;

/**
* Implements hook_drush_command().
*/
function rp_api_drush_command() {

  $commands['rp-api-import'] = [
    'description' => 'RP API Import entities',
    'callback' => 'rp_api_drush_import_model',
    'aliases' => ['rpapii'],
    'arguments'=>[
                    'model' => 'Model',
                 ],
    'options' => [
                  'query' => 'Query sting with parameters',
                  'update' => 'Enable updates (0,1)',
                  'debug-message' => 'Debug messages (0,1) (debug option is standard for drush)'
                  ],
    'examples' => [
                  'drush rpapii sites' => 'Import Sites entity from API STEVE',
                  'drush rpapii site --query="id=3"' => 'Import Site entity from API STEVE with query string id=3',
                  'drush rpapii sites --update=1' => 'Import Sites entity from API STEVE. Default is false',
                  ],
  ];

  $commands['rp-api-list'] = [
    'description' => 'RP API List plugin models available',
    'callback' => 'rp_api_drush_list_model',
    'aliases' => ['rpapil'],
    'examples' => [
      'drush rpapil' => 'List models of import',
    ],
  ];

  $commands['rp-api-entity-list'] = [
    'description' => 'RP Entity show all entities from entity type',
    'callback' => 'rp_base_drush_show_all_entities',
    'aliases' => ['rpapiel'],
    'arguments' => [
      'entity' => 'Entity',
    ],
    'examples' => [
      'drush rpapiel site' => 'Show all entities for "site" type',
    ],
  ];

  return $commands;
}


function rp_api_drush_import_model($model) {
  $query_str = drush_get_option('query', '');
  $update = (int)drush_get_option('update', 0);
  $debug = (int)drush_get_option('debug-message', 0);
  parse_str($query_str, $query_vars);

  /** @var RPAPIModelManager $manager */
  $manager = \Drupal::service('rp_api.plugin.model.manager');

  try{
    $instance = $manager->createInstance($model, array());
  }
  catch(Exception $e){
    return drush_set_error('DRUSH_FRAMEWORK_ERROR',$e->getMessage().' Try drush rpapil to see all models/plugins');
  }

  $descriptions = RPAPIDescription::getDescription();
  $desc_methods = array_keys($descriptions['operations']);

  if(!in_array($instance->api_method(),$desc_methods)) {
    return drush_set_error('DRUSH_FRAMEWORK_ERROR', dt('Method does not exist: !id', array('!id' => $instance->getPluginId())));
  }
  if(isset($descriptions['operations'][$instance->api_method()]['parameters'])) {
    $desc_parameters = array_keys($descriptions['operations'][$instance->api_method()]['parameters']);
    //if(array_keys($query_vars) != $desc_parameters) {
    //  return drush_set_error('DRUSH_FRAMEWORK_ERROR', dt('Required parameters does not exist in query request: !query', array('!query' => implode(',',$desc_parameters))));
    //}
  }

  drush_print('Plugin ID/Model: '.$instance->getPluginId().' Description: '.$instance->description());

  $entities = $instance->import($update, $query_vars, $debug);

  if($update)
    drush_print(dt('Update is enabled'));
  else
    drush_print(dt('Update is disabled'));

  if(is_null($entities) || empty($entities)){
    drush_print(dt('No entities created or updated'));
  }
  else if(is_array($entities)) {
    $drush_entities = 'Entities created or updated:'."\n";
    foreach($entities as $entity){
      if(is_object($entity))
        $drush_entities .= $entity->id().' '.$entity->label()."\n";
    }
  }
  else if(is_object($entities)) {
    $drush_entities = 'Entity created or updated:'."\n";
    $drush_entities .= $entities->id().' '.$entities->label()."\n";
  }
  else {
    $drush_entities = 'No message';
  }

  drush_print($drush_entities);

}

function rp_api_drush_list_model(){

  /** @var RPAPIModelManager $manager */
  $manager = \Drupal::service('rp_api.plugin.model.manager');

  $items = [];
  foreach($manager->getDefinitions() as $definition){
    $items[] = '- Plugin ID/Model: '.$definition['id']."\n".'  Entity imported: '.$definition['entity']."\n".'  API Method: '.$definition['api_method']."\n".'  Description: '.$definition['description'];
  }

  drush_print(dt('List of model plugin definitions loaded'));
  drush_print(implode("\n",$items));

}

function rp_base_drush_show_all_entities($entity) {
  if(is_null($entity))
    return drush_set_error('DRUSH_FRAMEWORK_ERROR', dt('Entity is not defined'));

  $ids = \Drupal::entityQuery($entity)->execute();
  $storage_handler = \Drupal::entityTypeManager()->getStorage($entity);
  $entities = $storage_handler->loadMultiple($ids);

  $items = [];
  $items[] = implode("\t\t\t",['ID','API ID','API Code','Label']);
  foreach($entities as $entity_load) {
    $items[] = implode("\t\t\t",[
      $entity_load->id(),
      method_exists($entity_load,'getApiId') ? $entity_load->getApiId() : '',
      method_exists($entity_load,'getApiCode') ? $entity_load->getApiCode() : '',
      $entity_load->label(),
    ]);
  }
  drush_print(implode("\n",$items));
}