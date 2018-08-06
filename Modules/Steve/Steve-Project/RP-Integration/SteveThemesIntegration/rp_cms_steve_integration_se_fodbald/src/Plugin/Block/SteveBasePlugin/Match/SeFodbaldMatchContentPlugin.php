<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Match;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldmatchcontentplugin' block.
 *
 * @Block(
 *  id = "sefodbaldmatchcontentplugin",
 *  admin_label = @Translation("Se Fodbald Match Content Plugin"),
 * )
 */
class SeFodbaldMatchContentPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController(); 
	
	$schedule = [];
	
	if($node = $class->getNodeByUrl(1)){
		$conditions = [
			[
				'field' => 'field_event_tournament',
				'value' => $node->field_event_tournament->target_id,
				'operator' => '='
			]
		];

		$schedule = $class->getSchedulePlusTree(0, 'Y-m-d', 14, 0, $class->getSport(2, 'api'), NULL, $conditions);
	}

    $data = [
      '#theme' => 'sefodbaldmatchcontentplugin',
      '#tags' => $class->getEvents('node', 1, true),
      '#schedule' => $schedule,
      '#events' => $class->getEvents('league', 20),
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

