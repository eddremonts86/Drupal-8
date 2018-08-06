<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Providers;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldproviderstabalt' block.
 *
 * @Block(
 *  id = "sefodbaldproviderstabalt",
 *  admin_label = @Translation("Se Fodbald Providers Tab Alt"),
 * )
 */
 
class SeFodbaldProvidersTabAlt extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController();
	$event = [];
	
	if($class->getNodeByUrl(1)){
		$event = $class->getEvents('node', 1, true);
	}else if($class->getTaxonomyTermByUrl()){
		$event = $class->getEvents('term', 1, true);
	}
	
    $data =  [
      '#theme' => 'sefodbaldproviderstabalt',
      '#tags' => $event,
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
