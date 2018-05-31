<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Program;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldprogrambreadcrumbsplugin' block.
 *
 * @Block(
 *  id = "sefodbaldprogrambreadcrumbsplugin",
 *  admin_label = @Translation("Se Fodbald Program Breadcrumbs Plugin"),
 * )
 */
class SeFodbaldProgramBreadcrumbsPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController(); 
	  
    $data =  [
      '#theme' => 'sefodbaldprogrambreadcrumbsplugin',
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

