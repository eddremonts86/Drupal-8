<?php

namespace Drupal\sesport_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'sesportreviews' block.
 *
 * @Block(
 *  id = "sesportreviews",
 *  admin_label = @Translation("Live stream review(General)"),
 * )
 */
class sesport_review extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'sesport_review',
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

