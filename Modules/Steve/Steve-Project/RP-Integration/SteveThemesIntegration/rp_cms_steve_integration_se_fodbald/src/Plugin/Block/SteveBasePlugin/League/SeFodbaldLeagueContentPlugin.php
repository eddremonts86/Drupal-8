<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\League;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldleaguecontentplugin' block.
 *
 * @Block(
 *  id = "sefodbaldleaguecontentplugin",
 *  admin_label = @Translation("Se Fodbald League Content Plugin"),
 * )
 */
class SeFodbaldLeagueContentPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController(); 
	
	$schedule = [];
	
	if($term = $class->getTaxonomyTermByUrl()){
		$conditions = [
			[
				'field' => 'field_event_tournament',
				'value' => $term->id(),
				'operator' => '='
			]
		];
		$schedule = $class->getSchedulePlusTree(0, 'Y-m-d', 14, 0, $class->getSport(2, 'api'), NULL, $conditions);
	}
	
    $data =  [
      '#theme' => 'sefodbaldleaguecontentplugin',
      '#tags' => $class->getEvents('term', 1, true),
      '#info'=> $class->getSeFodbaldLeagueInfo(),
      '#schedule' => $schedule,
      '#events' => $class->getEvents('term', 20),
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

