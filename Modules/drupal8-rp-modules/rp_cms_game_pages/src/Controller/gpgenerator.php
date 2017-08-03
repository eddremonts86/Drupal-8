<?php

namespace Drupal\rp_cms_game_pages\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

use Drupal\rp_api\RPAPIClient;
use Drupal\rp_repo\RepoService;

/**
 * Class gpgenerator.
 *
 * @package Drupal\rp_cms_game_pages\Controller
 */
class gpgenerator extends ControllerBase {

  /**
   * Get data from content repo.
   *
   * @return string
   *   Return Hello string.
   */
  public function repodata() {
    $rpClient = RepoService::getClient();
    $regions = $rpClient->getSites();


    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: get data from content repo'),
    ];
  }

  /**
   * Apidata.
   *
   * @return string
   *   Return Hello string.
   */
  public function apidata() {

    $rpClient = RPAPIClient::getClient();
    $Allschedule = $rpClient->getschedule();

    foreach ($Allschedule['data'] as $sche) {
      $sche['time'];
      //---------- array ----------//
      $sche['competition_hierarchy'];
      $sche['competition'];;
      $sche['channels'];
      $sche['streams'];


      $taxonomy = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadByProperties(['name' => $sche['sport']['name']]);
      if (!$taxonomy) {
        $term = Term::create([
          'parent' => [],
          'name' => $sche['sport']['name'],
          'vid' => 'sport',
        ]);
        $term->save();
        $taxonomy = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['name' => $sche['sport']['name']]);
      }
      $term = reset($taxonomy);

      //Cambiar la comparacion a  field_api_gp_id‎ = api_id

      $id_node = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadByProperties(
            array(
              'type' => 'game_pages',
              'title' => $sche['name']
                )
          );

      if (!$id_node) {
        $node = Node::create([
          'type' => 'game_pages',
          'title' => $sche['name'],
          'field_participant_1' => $sche['participants'][0]['name'],
          'field_participant_2' => $sche['participants'][1]['name'],
          'field_tags' => $term->id(),


          'field_api_gp_id‎' => $sche['id'],
          'field_content_section_heading_1' => $sche['id'],
          'field_content_section_heading_2' => $sche['id'],
          'field_h2h_section_graph_heading' => $sche['id'],
          'field_h2h_section_' => $sche['id'],
          'field_promo_stream_provider_butt' => $sche['id'],
          'field_promo_box_head_1' => $sche['id'],
          'field_promo_stream_provider_poin' => $sche['id'],
          'field_promo_stream_provider_head' => $sche['id'],
          'field_pspbs_tow' => $sche['id'],
          'field_tabs_section_se_on_tv_head' => $sche['id'],

        ]);

        $node->save();
        exit();
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: apidata'),
    ];
  }

}
