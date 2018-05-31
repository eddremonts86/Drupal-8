<?php

namespace Drupal\rp_cms_steve_integration\Plugin\Block\SteveBasePlugin\Participant;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\rp_cms_steve_integration\Controller\SteveFrontendControler;
/**
 * Provides a 'ParticipantHeadPlugin' block.
 *
 * @Block(
 *  id = "participantheadplugin",
 *  admin_label = @Translation("Participant Page Head"),
 * )
 */
class ParticipantHeadPlugin extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'participantheadplugin',
      '#titulo' => 'Mi titulo sesportblocks',
      '#descripcion' => 'Mi descripción sesportblocks',
      '#tags' => $this->getInfo(),
      '#colors' => $this->getColors(),
      '#background' => $this->background()
    ];
  }

  public function getColors(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getColors();
    return $data;
  }

  public function background(){
    $objGet = new SteveFrontendControler();
    $data = $objGet->getEventBackgroundByTaxonomy();
    return $data;
  }

  public function getInfo(){
    $obj = new SteveFrontendControler();
    $data = $obj->getEventHeadByTaxonomy();
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