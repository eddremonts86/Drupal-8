<?php

namespace Drupal\rp_api\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\rp_api\RPAPIClient;

/**
 * Returns responses for RP API routes.
 */
class RPAPIController extends ControllerBase {

  /**
   * The date formatter service.
   *
   * @var DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs the controller object.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   *
   * @DCG: Optional.
   */
  public function __construct(DateFormatterInterface $date_formatter) {
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * Builds the response.
   */
  public function test() {

    $rpClient  = RPAPIClient::getClient();
    $sites = $rpClient->getSites();
    dsm($sites);

    foreach($sites as $site) {
      $data = array(
        'title' => $site['name'] . ' ' . uniqid()
      );
      $site_new = \Drupal::entityManager()->getStorage('rp_site')->create($data);
      $site_new->save();
    }
    


    $build['content'] = [
      '#type' => 'item',
      '#title' => t('Content'),
      '#markup' => t('Hello world!'),
    ];

    return $build;
  }

}
