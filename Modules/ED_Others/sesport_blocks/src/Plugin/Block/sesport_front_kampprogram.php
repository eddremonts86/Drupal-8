<?php

namespace Drupal\sesport_blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'sesport_front_kampprogram' block.
 *
 * @Block(
 *  id = "sesport_front_kampprogram",
 *  admin_label = @Translation("Kampprogram"),
 * )
 */
class sesport_front_kampprogram extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'sesport_front_kampprogram',
            '#titulo' => 'Mi titulo sesportblocks',
            '#descripcion' => 'Mi descripción sesportblocks',
            '#tags' => $this->getdata()
        ];
    }
    public function getdata()
    {
        $all_nodes = "";
        $nids = \Drupal::entityQuery('node')->condition('type', 'stream_provider')->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        foreach ($nodes as $node) {
            $all_nodes[] = $node->toArray();
        }
        return $all_nodes;
    }
}

