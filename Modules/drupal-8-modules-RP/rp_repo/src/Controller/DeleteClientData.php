<?php

namespace Drupal\rp_repo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class DeleteClientData.
 */
class DeleteClientData extends ControllerBase
{

    /**
     * Deleteclientdata.
     *
     * @return string
     *   Return Hello string.
     */


    public function DeleteClientData()
    {

        $data = [
          'game_pages',
          'team_content',
          'tournament_page',
          'channels',
          'sport_internal_pages',
          'sport_internal_blogs',
          'sport_pages',
          'stream_provider',
        ];
        for ($i = 0; $i < count($data); $i++) {
            $query = \Drupal::entityQuery('node');
            $query->condition('type', $data[$i]);
            $ids = $query->execute();
            $this->deleteNodeProccess($ids, $data[$i]);
        }
        $this->deleteEntityProccess('channels_by_contenttype');
        $this->deleteTaxonomyItems();
        echo "All contents had been delete ";
        echo "\n";
        echo "\n";
        echo "\n";
        return true;
    }

    public function deleteNodeProccess($ids, $name)
    {
        foreach ($ids as $nid) {
            $node = NODE::load($nid);
            $node->delete();
            echo "Delete node type '" . $name . "' :" . $nid;
            echo "\n";
        }
        return true;
    }

    public function deleteEntityProccess($entity_type)
    {
        $query = \Drupal::entityQuery($entity_type);
        $ids = $query->execute();
        foreach ($ids as $nid) {
            $entity = \Drupal::entityTypeManager()
              ->getStorage($entity_type)
              ->load($nid);
            $entity->delete();
            echo "Delete Entity : " . $nid;
            echo "\n";
        }
        return true;
    }

    public function deleteTaxonomyItems()
    {
           $query = \Drupal::entityQuery('taxonomy_term');
           $ids = $query->execute();
           foreach ($ids as $id){
                $taxonomy = Term::load($id);
                if(!empty($taxonomy)){
                    $taxonomy->delete();
                }
               echo "Delete Taxonomy : " . $id;
               echo "\n";
            }
        return true;

    }

}
