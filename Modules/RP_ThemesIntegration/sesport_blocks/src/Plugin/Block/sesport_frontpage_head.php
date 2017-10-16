<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\sesport_blocks\Controller\getCMSdata;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Provides a 'sesportfrontpagehead' block.
 *
 * @Block(
 *  id = "sesportfrontpagehead",
 *  admin_label = @Translation("Sesport - Head (Sport Page)"),
 * )
 */
class sesport_frontpage_head extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
          '#theme' => 'sesport_frontpage_head',
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
        $sportName =  $objGet->getSport();
        $nodes = $objGet->getSchedule(1,$sportName['sportApiId']);
        $output = $objGet->getSubmenu();
        $url = Url::fromRoute('entity.node.canonical', ['node' => $nodes[0]["nid"][0]["value"]])->toString();

        $data = [
          ['sportName' => $sportName['sportName']],
          ['node'=>$nodes],
          ['sportApiId' => $sportName['sportApiId']],
          ['sportImgUrl' => $sportName['sportImgUrl']],
          ['output' => $output],
          ['alias' => $url]
        ];
     /*   echo"<pre>";
        var_dump($data);
        exit();*/

        return $data ;
    }
}

