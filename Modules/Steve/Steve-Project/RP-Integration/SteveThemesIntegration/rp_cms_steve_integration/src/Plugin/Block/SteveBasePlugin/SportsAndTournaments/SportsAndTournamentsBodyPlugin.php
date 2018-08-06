<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\SportsAndTournaments;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'SportsAndTournamentsBodyPlugin' block.
 *
 * @Block(
 *  id = "sportsandtournamentsbodyplugin",
 *  admin_label = @Translation("Sports And Tournaments Page Body"),
 * )
 */
class SportsAndTournamentsBodyPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sportsandtournamentsbodyplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo()
    ];
  }

  public function getInfo(){
    $obj = new SteveFrontendControler();
    $data = $obj->getEventBodyByTaxonomy();
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