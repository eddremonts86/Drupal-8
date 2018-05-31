<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Match;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldmatchbreadcrumbsplugin' block.
 *
 * @Block(
 *  id = "sefodbaldmatchbreadcrumbsplugin",
 *  admin_label = @Translation("Se Fodbald Match Breadcrumbs Plugin"),
 * )
 */
class SeFodbaldMatchBreadcrumbsPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $class = new SeLiveFodbaldController();
    
    $data =  [
      '#theme' => 'sefodbaldmatchbreadcrumbsplugin',
      '#tags' => $class->getSeFodbaldBreadcrumb(),
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

