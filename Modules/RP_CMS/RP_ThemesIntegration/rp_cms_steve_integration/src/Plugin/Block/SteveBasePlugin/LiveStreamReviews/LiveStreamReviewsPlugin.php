<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\LiveStreamReviews;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'LiveStreamReviewsPlugin' block.
 *
 * @Block(
 *   id = "livestreamreviewsplugin",
 *   admin_label = @Translation("Live Stream Reviews Block"),
 * )
 */
class LiveStreamReviewsPlugin extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'livestreamreviewsplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo()
    );
  }
  public function getInfo(){
  	$obj = new SteveFrontendControler();
  	$data = $obj->getLiveStreamReviewsFormat();
    return $data;
  }
  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }


}