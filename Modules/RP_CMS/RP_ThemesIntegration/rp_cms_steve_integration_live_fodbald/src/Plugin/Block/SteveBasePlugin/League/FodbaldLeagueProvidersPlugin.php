<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\League;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldleagueprovidersplugin' block.
 *
 * @Block(
 *  id = "fodbaldleagueprovidersplugin",
 *  admin_label = @Translation("Fodbald League Providers Plugin"),
 * )
 */
class FodbaldLeagueProvidersPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$controllerObject = new LiveFodbaldController();
	
    $data =  [
      '#theme' => 'fodbaldleagueprovidersplugin',
      '#tags' => $controllerObject->getFodbaldMatchData('term', 1, TRUE),
      '#schedule' => $controllerObject->getFodbaldSchedule('league', 7),
      '#info' => $controllerObject->getLeaguePageInfo(),
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

