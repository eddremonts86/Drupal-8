<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\SportsAndTournaments;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'SportsAndTournamentsListPlugin' block.
 *
 * @Block(
 *  id = "sportsandtournamentslistplugin",
 *  admin_label = @Translation("Sports And Tournaments Page List"),
 * )
 */
class SportsAndTournamentsListPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $element = 0;
    $build['result'] = [
      '#theme' => 'sportsandtournamentslistplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' =>  $this->getInfo(),
      '#colors' => $this->getColors()
    ];

    $build['pager'] = [
      '#type' => 'pager',
      '#element' => $element,
      '#quantity' => 2,
      '#tags' => array('<<','<','','>','>>')
    ];

    return $build;
  }

  public function getInfo(){
    $obj = new SteveFrontendControler();
    $data = $obj->getFormatedEventList(15);
    return $data;
  }

  public function getColors(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getColors();
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