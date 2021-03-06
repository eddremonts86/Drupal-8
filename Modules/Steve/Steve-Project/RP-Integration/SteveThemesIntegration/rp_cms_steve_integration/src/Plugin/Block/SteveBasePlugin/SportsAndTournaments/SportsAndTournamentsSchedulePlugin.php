<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\SportsAndTournaments;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'SportsAndTournamentsSchedulePlugin' block.
 *
 * @Block(
 *  id = "sportsandtournamentsscheduleplugin",
 *  admin_label = @Translation("Sports And Tournaments Page Schedule"),
 * )
 */
class SportsAndTournamentsSchedulePlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sportsandtournamentsscheduleplugin',
      '#titulo' => 'Site Footer',
      '#descripcion' => '',
      '#tags' => $this->getInfo(),
      '#colors' => $this->getColors()
    ];
  }
  public function getInfo(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getEventListSchedule();
    return $data;
  }
  public function getColors(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getColors();
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
