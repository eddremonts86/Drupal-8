<?php

namespace Drupal\rebelpenguin_endponts_client\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Simple page controller for drupal.
 */
class Page extends ControllerBase {

  /**
   * Lists the examples provided by form_example.
   */
  public function description() {
    // These libraries are required to facilitate the ajax modal form demo.
    $content['#attached']['library'][] = 'core/drupal.ajax';
    $content['#attached']['library'][] = 'core/drupal.dialog';
    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $content['intro'] = [
      '#markup' => '<p>' . $this->t('Rebel Penguins Drupal Form API.') . '</p>',
    ];
    $content['links'] = [
      '#theme' => 'item_list',
      '#items' => [
       Link::createFromRoute($this->t('Endpoints URL configuration'), 'rebelpenguin_endponts_client.rebelpenguin_endponts_client_urls'),
       Link::createFromRoute($this->t('Import All Nodes by Content Tyoe'), 'rebelpenguin_endponts_client.rebelpenguin_endponts_client_importnodesbytypes'),
       Link::createFromRoute($this->t('Import Expesific Node and Revisions'), 'rebelpenguin_endponts_client.rebelpenguin_endponts_client_import')
      ],
    ];
    $content['message'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'fapi-example-message'],
    ];
    return $content;
  }

}
