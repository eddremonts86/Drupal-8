<?php

namespace Drupal\sesport_blocks\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'sesport_front_stream' block.
 *
 * @Block(
 *  id = "sesport_front_stream",
 *  admin_label = @Translation("Live stream(Front Page)"),
 * )
 */
class sesport_front_stream extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'sesport_front_stream',
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

