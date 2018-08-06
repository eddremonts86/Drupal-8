<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 6/18/18
 * Time: 1:35 PM
 */

namespace Drupal\rp_block_data_configuration\Controller;

use Drupal\Core\Controller\ControllerBase;

class BlocksDataConfigs extends ControllerBase
{
  public function createConfigBlock($form_state, $block_id)
  {


    $database = \Drupal::database();
    $data = $form_state->getValues();
    $exit = $database->select('configuration_block_list', 'x')
      ->fields('x', array('block_id', 'active'))
      ->condition('x.block_id', $block_id, '=')
      ->execute()->fetchAll();


    if (!isset($exit) or empty($exit)) {
      if ($data['configExternal'] == 1) {
        $database->insert('configuration_block_list')
          ->fields([
            'block_id' => $block_id,
            'active' => $data['configExternal'],
          ])->execute();
        $database->insert('configuration_block')
          ->fields([
            'block_id' => $block_id,
            'block_name' => $block_id,
            'active' => $data['configExternal'],
            'tournaments_weight' => 0,
            'event_number' => 0,
            'only_promotion' => 0,
            'tournaments_list' => 0,
          ])->execute();

      }
      drupal_set_message('We has saved the data!!!');
      return true;
    }

    if (isset($exit) and ($data['configExternal'] == 0 or !isset($data['configExternal'])) and $exit[0]->active == 1) {
      $database->update('configuration_block_list')
        ->condition('block_id', $block_id, '=')
        ->fields(['active' => 0])
        ->execute();
      drupal_set_message('We has saved the data!!!');
      return true;
    } else if (isset($exit) and $data['configExternal'] == 1 and $exit[0]->active == 0) {
      $database->update('configuration_block_list')
        ->condition('block_id', $block_id, '=')
        ->fields(['active' => 1])
        ->execute();
      drupal_set_message('We has saved the data!!!');
      return true;
    }
  }

  public function getBlockToConfig()
  {
    $data = array();
    $database = \Drupal::database();
    $exit = $database->select('configuration_block_list', 'x');
    $exit->leftJoin('configuration_block', 'xx', 'xx.block_id=x.block_id');
    $exit->fields('x', array('block_id', 'active'))
      ->fields('xx', array('tournaments_weight', 'event_number', 'only_promotion', 'tournaments_list'))
      ->condition('x.active', 1);
    $blocks = $exit->orderBy('x.block_id')->execute()->fetchAll();

    if (isset($blocks)) {
      foreach ($blocks as $block) {
        $data[] = [
          'blockid' => $block->block_id,
          'blockname' => $block->block_name ? $block->block_name:$block->block_id,
          'active' => $block->active,
          'tournaments_weight' => $block->tournaments_weight,
          'event_number' => $block->event_number,
          'only_promotion' => $block->only_promotion,
          'tournaments_list' => $block->tournaments_list,
        ];
      }
    }

    return $data;
  }


  public function getBlock($block_id)
  {
    $database = \Drupal::database();
    $data = 0;
    $exit = $database->select('configuration_block_list', 'x')
      ->fields('x', array('block_id', 'active'))
      ->condition('x.block_id', $block_id, '=')
      ->execute()->fetchAll();
    if (isset($exit)) {
      foreach ($exit as $blocks) {
        $data = $blocks->active;
      }
    }
    return $data;
  }

  public function saveBlockConfiguration($obj)
  {
    $database = \Drupal::database();
    $save = $database->update('configuration_block')
      ->condition('block_id', $obj['blockid'], '=')
      ->fields([
        'tournaments_weight' => $obj['tournaments_weight'],
        'event_number' => $obj['event_number'],
        'only_promotion' => $obj['only_promotion'],
        //'tournaments_list' => $obj['tournaments_list']
      ])->execute();

    if ($save) {
      drupal_set_message('We has saved the data!!!');
      return true;
    } else {
      drupal_set_message('Some error has occurred');
      return false;
    }
  }
}
