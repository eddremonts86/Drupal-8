<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald_streams\Plugin\Block\SteveBasePlugin\Team;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald_streams\Controller\LiveFodbaldStreamsController;

/**
 * Provides a 'fodbaldstreamsteamcontentplugin' block.
 *
 * @Block(
 *  id = "fodbaldstreamsteamcontentplugin",
 *  admin_label = @Translation("Fodbald Streams Team Content Plugin"),
 * )
 */
class FodbaldStreamsTeamContentPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new LiveFodbaldStreamsController();
    $data =  [
      '#theme' => 'fodbaldstreamsteamcontentplugin',
      '#tags' => $class->getTermInfo(),
      '#events' => $class->getEvents('team', null),
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

