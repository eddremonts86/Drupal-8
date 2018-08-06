<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 2/22/18
 * Time: 3:24 PM
 */

namespace Drupal\rp_repo\Controller\entities\Pages;


use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\rp_api\RPAPIClient;


 abstract class pages extends ControllerBase
{
  public function createPages($obj)
  {
    $node = Node::create($obj);
    $node->save();
    return $node->id();

  }
  public function createNodeGeneric($data)
  {
    $node = Node::create($data);
    $node->save();
    return $node->id();
  }
  public function updatePages($obj)
  {
  }


  /*   -----   Delete Process   ------   */
  public function deletePages($nid)
  {
    $node = NODE::load($nid);
    $node->delete();
    return true;
  }
  public function deleteNodeByType($type)
  {
    $query = \Drupal::entityQuery('node')->condition('type', $type);
    $ids = $query->execute();
    foreach ($ids as $nid) {
      $node = NODE::load($nid);
      echo "Delete node type '" . $node->title() . "' :" . $nid;
      echo "\n";
      $node->delete();
    }
    return true;
  }
  public function deleteAllNode()
  {
    $query = \Drupal::entityQuery('node');
    $ids = $query->execute();
    foreach ($ids as $nid) {
      $node = NODE::load($nid);
      echo "Delete node type '" . $node->getTitle() . "'";
      echo "\n";
      $node->delete();
    }
    return true;
  }

   /*   -----   Gets   ------   */
   public function getNode($fieldData, $type, $field)
   {
     $id_node = \Drupal::entityTypeManager()
       ->getStorage('node')
       ->loadByProperties(['type' => $type, $field => $fieldData]);
     return $id_node;
   }


}
