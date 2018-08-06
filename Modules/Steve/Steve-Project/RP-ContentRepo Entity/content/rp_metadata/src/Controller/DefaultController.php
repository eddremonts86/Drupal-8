<?php

namespace Drupal\rp_metadata\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\rp_metadata\MetadataFieldClient;
/**
 * Class DefaultController.
 *
 * @package Drupal\rp_metadata\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {

    $c = MetadataFieldClient::load();
    var_dump($c->get('tracks'));

    $m = new MetadataFieldClient();
    var_dump($m->getHierarchicalByEntityAndField('sport_info', 'field_sport_info_heading'));
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $name'),
    ];
  }

}
