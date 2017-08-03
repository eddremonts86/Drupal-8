<?php

namespace Drupal\rp_site\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rp_api\RPAPIClient;
use Drupal\rp_api\Utility\RPAPIHelper;
use Drupal\rp_api\ChangedFields\RPAPIChangedFiles;

/**
 * Class DefaultController.
 *
 * @package Drupal\rp_site\Controller
 */
class DefaultController extends ControllerBase implements ContainerInjectionInterface  {

  /**
   * The rpApiHelper.
   *
   * @var Drupal\rp_api\Utility\RPAPIHelper
   */
  protected $rp_api;

  /**
   *
   * @param Drupal\rp_api\Utility\RPAPIHelper
   */
  public function __construct(RPAPIHelper $RPAPIHelper) {
    $this->rp_api = $RPAPIHelper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('rp_api.helper')
    );
  }

  /**
   * Test.
   *
   * @return string
   *   Return Hello string.
   */
  public function test() {

    return [
      '#type' => 'markup',
      '#markup' => $this->t('API Saved')
    ];
  }

}
