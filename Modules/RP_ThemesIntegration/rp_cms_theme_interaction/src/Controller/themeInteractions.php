<?php

namespace Drupal\rp_cms_theme_interaction\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;


/**
 * Class themeInteractions.
 */
class themeInteractions extends ControllerBase {

  public function getSchedulePlusTree($range = 0, $sport_name = 'Fodbold', $format = "Y-m-d") {
    $nodes = $this->getSchedule($range, $sport_name);
    $tree = $this->getTree($nodes, $format);
    return $tree;

  }

  public function getSchedule($range = 0, $sport_name = 'Fodbold') {
    $sport = $this->getTaxonomy($sport_name);
    $sport = reset($sport);
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'game_pages');
    $query->condition('field_tags', $sport->id());
    //$fecha = strtotime(date('Y-05-j'));
    //$query->condition('field_game_date',$fecha, '<');
    $query->sort('field_game_date', 'ASC');
    $query->sort('field_game_tournament_api_id', 'ASC');
    if ($range != 0) {
      $query->range(0, $range);
    }
    $ids = $query->execute();
    $all_nodes = $this->getNodes($ids);
    return $all_nodes;
  }

  public function getScheduleTreebyDate($sport_name = 'Fodbold', $format = "Y-m-d") {
    $startdate = date('Y-05-23 00:00:00');
    $enddate = date('Y-05-23 23:59:59');
    $date = [
      'startdate' => $startdate,
      'enddate' => $enddate
    ];
    $nodes = $this->getSchedulebyDate($date, $sport_name);
    $tree = $this->getTree($nodes, $format);
    return $tree;

  }

  public function getSchedulebyDate ($date,$sport_name = 'Fodbold') {
    $startdate = strtotime($date['startdate']);
    $enddate = strtotime($date['enddate']);
    $sport = $this->getTaxonomy($sport_name);
    $sport = reset($sport);
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'game_pages');
    $query->condition('field_tags', $sport->id());
    $query->condition('field_game_date',$startdate, '>');
    $query->condition('field_game_date',$enddate, '<');
    $query->sort('field_game_date', 'ASC');
    $query->sort('field_game_tournament_api_id', 'ASC');
    $ids = $query->execute();
    $all_nodes = $this->getNodes($ids);
    return $all_nodes;
  }

  public function getTree($data, $format) {
    $tree = [];
    foreach ($data as $event) {
      $date = date($format, $event['field_game_date'][0]['value']);
      $league = $event['field_game_tournament'][0]['value'];
      $tournament_id = $event['field_game_tournament_api_id'][0]['value'];
      if (!$tree['AllEvents'][$date]) {
        $tree['AllEvents'][$date] = [];
        $tree['AllEvents'][$date][$league]['events'] = [];
        $tree['AllEvents'][$date][$league]['tournament'] = $league;
        $tree['AllEvents'][$date][$league]['tournament_id'] = $tournament_id;
        array_push($tree['AllEvents'][$date][$league]['events'], $event);
      }
      else {
        if ($tree['AllEvents'][$date]) {
          if (!$tree['AllEvents'][$date][$league] and $tree['AllEvents'][$date][$league]['tournament_id'] != $tournament_id) {
            $tree['AllEvents'][$date][$league]['tournament'] = $league;
            $tree['AllEvents'][$date][$league]['tournament_id'] = $tournament_id;
            $tree['AllEvents'][$date][$league]['events'] = [];
            array_push($tree['AllEvents'][$date][$league]['events'], $event);
          }
          else {
            array_push($tree['AllEvents'][$date][$league]['events'], $event);
          }

        }
      }
    }
    return $tree;
  }

  public function getStream() {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'stream_provider');
    $query->sort('field_properties_rating', 'ASC');
    $ids = $query->execute();
    $all_nodes = $this->getNodes($ids);
    // return $all_nodes;
    return [
      '#type' => 'markup',
      '#markup' => $all_nodes,
    ];
  }

  public function getTaxonomy($name) {
    $taxonomy = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $name]);
    return $taxonomy;
  }

  public function getNodes($ids) {
    $all_nodes = [];
    foreach ($ids as $id) {
      $node = Node::load($id);
      $all_nodes [] = $node->toArray();
    }
    return $all_nodes;
  }

}
