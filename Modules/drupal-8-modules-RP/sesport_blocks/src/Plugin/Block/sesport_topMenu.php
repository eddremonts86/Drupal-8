<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Cache\Cache;
use Drupal\sesport_blocks\Controller\getCMSdata;

/**
 * Provides a 'sesporttopmenu' block.
 *
 * @Block(
 *  id = "sesporttopmenu",
 *  admin_label = @Translation("sesport top Menu"),
 * )
 */
class sesport_topMenu extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
          '#theme' => 'sesport_topMenu',
          '#titulo' => 'Mi titulo sesportblocks',
          '#descripcion' => 'Mi descripción sesportblocks',
          '#tags' => $this->getdata(),
        ];
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

    public function getdata()
    {
        $objGet = new getCMSdata();
        $node = \Drupal::routeMatch()->getParameter('node');
        $node = $node->toArray();
        $type = $node["type"][0]["target_id"];
        $sportName = 'football';
        if($type == 'sport_pages'){
            $sportName = $objGet->getClearUrl($node["field_sport_name"][0]["value"]);
        }
        else if($type == 'sport_internal_pages') {
            $sporttaxonomy = $node["field_sport_tax"][0]["target_id"];
            $sportNameTaxonomya = $objGet->getTaxonomyByID($sporttaxonomy)->toArray();
            $sportName = $objGet->getClearUrl($sportNameTaxonomya["name"][0]["value"]);
        }
        $sportName = [ 'sportName' => $sportName ];
        return $sportName ;
    }
}

