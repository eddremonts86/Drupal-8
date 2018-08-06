<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\SportsAndTournaments;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'SportsAndTournamentsHeadPlugin' block.
 *
 * @Block(
 *  id = "sportsandtournamentsheadplugin",
 *  admin_label = @Translation("Sports And Tournaments Page Head"),
 * )
 */
class SportsAndTournamentsHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sportsandtournamentsheadplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo(),
      '#colors' => $this->getColors(),
      '#background' => $this->getBackground()
    ];
  }

  public function getInfo(){
    $obj = new SteveFrontendControler();
    $data = $obj->getEventHeadByTaxonomy();
    return $data;
  }

  public function getColors(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getColors();
    return $data;
  }

  public function getBackground(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getEventBackgroundByTaxonomy();
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