<?php

namespace Drupal\rp_cms_steve_integration_horseracing\Plugin\Block\SteveBasePlugin\RaceCourses;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'horseracecourseslistplugin' block.
 *
 * @Block(
 *  id = "horseracecourseslistplugin",
 *  admin_label = @Translation("Horse Race Courses List Plugin"),
 * )
 */
class HorseRaceCoursesListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data =  [
      '#theme' => 'horseracecourseslistplugin',
      '#tags' => [],
      '#background' => [],
      '#colors' => []
    ];
    return $data;
  }

  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }
}

