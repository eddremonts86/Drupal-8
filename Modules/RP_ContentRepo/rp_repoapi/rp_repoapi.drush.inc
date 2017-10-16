<?php

use Drupal\rp_api\RPAPIModelManager;
use Drupal\rp_repoapi\RPRepoAPIDescription;

/**
* Implements hook_drush_command().
*/
function rp_repoapi_drush_command() {

  $commands['rp-repoapi-import'] = [
    'description' => 'RP API Import entities',
    'callback' => 'rp_repoapi_drush_import_model',
    'aliases' => ['rprepoapii'],
    'arguments'=>[
                    'model' => 'Model',
                 ],
    'options' => [
                  'query' => 'Query sting with parameters',
                  'update' => 'Enable updates (0,1)',
                  'debug-message' => 'Debug messages (0,1) (debug option is standard for drush)'
                  ],
    'examples' => [
                  'drush rprepoapii sites' => 'Import Sites entity from API STEVE',
                  'drush rprepoapii site --query="filter[id][value]=3"' => 'Import Site entity from API STEVE with query string id=3',
                  'drush rprepoapii sites --update=1' => 'Import Sites entity from API STEVE. Default is false',
                  ],
  ];


  return $commands;
}


function rp_repoapi_drush_import_model($model) {
  $query_str = drush_get_option('query', '');
  $update = (int)drush_get_option('update', 0);
  $debug = (int)drush_get_option('debug-message', 0);
  $query_vars = proper_parse_str($query_str);

  /** @var RPAPIModelManager $manager */
  $manager = \Drupal::service('rp_api.plugin.model.manager');

  try{
    $instance = $manager->createInstance($model, array());
  }
  catch(Exception $e){
    return drush_set_error('DRUSH_FRAMEWORK_ERROR',$e->getMessage().' Try drush rpapil to see all models/plugins');
  }

  $descriptions = RPRepoAPIDescription::getDescription();
  $desc_methods = array_keys($descriptions['operations']);

  if(!in_array($instance->api_method(),$desc_methods)) {
    return drush_set_error('DRUSH_FRAMEWORK_ERROR', dt('Method does not exist: !id', array('!id' => $instance->getPluginId())));
  }
  if(isset($descriptions['operations'][$instance->api_method()]['parameters'])) {
    $desc_parameters = array_keys($descriptions['operations'][$instance->api_method()]['parameters']);
    if(array_keys($query_vars) != $desc_parameters) {
      return drush_set_error('DRUSH_FRAMEWORK_ERROR', dt('Required parameters does not exist in query request: !query', array('!query' => implode(',',$desc_parameters))));
    }
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


function proper_parse_str($str) {
  # result array
  $arr = array();

  # split on outer delimiter
  $pairs = explode('&', $str);

  # loop through each pair
  foreach ($pairs as $i) {
    # split into name and value
    list($name,$value) = explode('=', $i, 2);

    # if name already exists
    if( isset($arr[$name]) ) {
      # stick multiple values into an array
      if( is_array($arr[$name]) ) {
        $arr[$name][] = $value;
      }
      else {
        $arr[$name] = array($arr[$name], $value);
      }
    }
    # otherwise, simply stick it in a scalar
    else {
      $arr[$name] = $value;
    }
  }

  # return result array
  return $arr;
}