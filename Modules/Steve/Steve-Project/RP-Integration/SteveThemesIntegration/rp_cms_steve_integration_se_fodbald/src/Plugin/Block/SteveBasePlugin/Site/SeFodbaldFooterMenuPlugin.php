<?php

namespace Drupal\rp_cms_steve_integration_se_fodbald\Plugin\Block\SteveBasePlugin\Site;

use Drupal\rp_cms_steve_integration_se_fodbald\Controller\SeLiveFodbaldController;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'sefodbaldfootermenuplugin' block.
 *
 * @Block(
 *  id = "sefodbaldfootermenuplugin",
 *  admin_label = @Translation("Se Fodbald Footer Menu Plugin"),
 * )
 */
class SeFodbaldFooterMenuPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$class = new SeLiveFodbaldController(); 
	   
    $data =  [
      '#theme' => 'sefodbaldfootermenuplugin',
      '#tags' => $class->buildFooterMenu('se_fodbold_footer_menu', 'se_fodbald'),
      '#help' => $class->buildMenu('sefodbaldhelpmenu'),
      '#socials' => $class->getSocialNetworks(),
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



