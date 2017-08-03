<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'sesportblocks' block.
 *
 * @Block(
 *  id = "sesportblocks",
 *  admin_label = @Translation("Live stream review(by name)"),
 * )
 */
class sesport_blocks extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'sesport_blocks',
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

