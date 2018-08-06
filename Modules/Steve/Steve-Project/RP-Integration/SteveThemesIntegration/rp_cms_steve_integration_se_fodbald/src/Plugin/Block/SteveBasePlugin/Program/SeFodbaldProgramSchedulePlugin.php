<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Program;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldprogramscheduleplugin' block.
 *
 * @Block(
 *  id = "sefodbaldprogramscheduleplugin",
 *  admin_label = @Translation("Se Fodbald Program Schedule Plugin"),
 * )
 */
class SeFodbaldProgramSchedulePlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController(); 
		
    $data =  [
      '#theme' => 'sefodbaldprogramscheduleplugin',
      '#tags' => $class->getSchedulePlusTree(0, "Y-m-d", 14, 0, $class->getSport(2, 'api')),
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

