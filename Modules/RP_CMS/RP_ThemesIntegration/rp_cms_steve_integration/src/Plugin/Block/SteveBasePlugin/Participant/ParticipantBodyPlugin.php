<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Participant;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'ParticipantBodyPlugin' block.
 *
 * @Block(
 *  id = "participantbodyplugin",
 *  admin_label = @Translation("Participant Page Body"),
 * )
 */
class ParticipantBodyPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $build['result'] = [
      '#theme' => 'participantbodyplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' =>  $this->getInfo()
    ];

    return $build;
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