<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\League;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamsleaguecontentplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsleaguecontentplugin",
 *  admin_label = @Translation("Fodbald Streams League Content Plugin"),
 * )
 */
class FodbaldStreamsLeagueContentPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController();
	
    $data =  [
      '#theme' => 'fodbaldstreamsleaguecontentplugin',
      '#tags' => $class->getTermInfo(),
      '#schedule' => $class->getEvents('term', null),
      '#teams' => $class->getTeamList(TRUE),	
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

