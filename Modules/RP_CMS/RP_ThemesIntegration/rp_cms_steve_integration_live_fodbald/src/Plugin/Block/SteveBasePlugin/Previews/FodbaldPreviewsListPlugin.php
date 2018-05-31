<?php

namespace Drupal\rp_cms_steve_integration_live_fodbald\Plugin\Block\SteveBasePlugin\Previews;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration_live_fodbald\Controller\LiveFodbaldController;

/**
 * Provides a 'fodbaldpreviewslistplugin' block.
 *
 * @Block(
 *  id = "fodbaldpreviewslistplugin",
 *  admin_label = @Translation("Fodbald Previews List Plugin"),
 * )
 */
class FodbaldPreviewsListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$data = [];
    $data['result'] = [
      '#theme' => 'fodbaldpreviewslistplugin',
      '#tags' => $this->getPreviews(),
      '#background' => [],
      '#colors' => []
    ];
    
    $data['pager'] = [
      '#type' => 'pager',
      '#element' => 0,
      '#tags' => array('<<','<','','>','>>')
    ];
    
    return $data;
  }
  
  public function getPreviews(){
  	$controllerObject = new LiveFodbaldController();
	return $controllerObject->getFodbaldPreviews(null, 10, null);	
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

