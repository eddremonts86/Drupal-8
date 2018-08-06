<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Participant;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;


/**
 * Provides a 'ParticipantSchedulePlugin' block.
 *
 * @Block(
 *  id = "participantscheduleplugin",
 *  admin_label = @Translation("Participant Page Schedule"),
 * )
 */
class ParticipantSchedulePlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'participantscheduleplugin',
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
