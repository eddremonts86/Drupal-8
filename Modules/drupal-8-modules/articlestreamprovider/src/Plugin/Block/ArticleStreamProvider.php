<?php

namespace Drupal\articlestreamprovider\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

/**
 * Created by PhpStorm.
 * User: edd
 * Date: 4/24/17
 * Time: 10:58 AM
 * Definición de nuestro bloque
 *
 * @Block(
 *   id = "streamProviders_custom",
 *   admin_label = @Translation("Custom Stream Providers")
 * )
 */
class ArticleStreamProvider extends BlockBase {
  /**
   * {@inheritdoc}
   */

  public function build() {
    $dataid = $this->stramegenerator();
    return [
      '#theme' => 'steram_theme',
      '#titulo' => 'Mi titulo super guay',
      '#descripcion' => 'Mi descripción super guay',
      '#tags' => $dataid
    ];
  }

  public function stramegenerator() {
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    $system_path = \Drupal::service('path.alias_manager')
      ->getPathByAlias($url[0]);
    $system_path = explode('node/', $system_path);
    $node = Node::load($system_path[1]);

    if ($node) {
      if ($node->getType() == 'article') {
        $node_tags = $node->get('field_stream_providers_')->getValue();
        $nids = \Drupal::entityQuery('node')
          ->condition('type', 'stream_provider')
          ->execute();
        $all_nodes = array();
        foreach ($node_tags as $node_tag) {
          foreach ($nids as $node) {
            $node = Node::load($node)->toArray();
            /*--------------------------Get images---------- */
              $id_1= $node["field_img_1_"][0]["target_id"];
              $id_2= $node["field_img_2_"][0]["target_id"];
              $id_3= $node["field_img_3_"][0]["target_id"];
              $img_1 = File::load($id_1)->toArray();
              $img_2 = File::load($id_2)->toArray();
              $img_3 = File::load($id_3)->toArray();
              $images = [
                        'img1'=> $img_1["uri"][0]["value"],
                        'img2'=> $img_2["uri"][0]["value"],
                        'img3'=> $img_3["uri"][0]["value"]
                       ];
              $node['images']= $images;
            /*--------------------------Get images---------- */

            $tag = $node['field_stream_providers_end'][0]['target_id'];
            if ($node_tag['target_id'] == $tag) {
              $all_nodes[] = $node;
            }
          }
        }
      }
    }
    return $all_nodes;


  }

}