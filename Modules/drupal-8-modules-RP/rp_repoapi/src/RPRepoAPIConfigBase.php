<?php

namespace Drupal\rp_repoapi;

use Drupal\Component\Plugin\PluginBase;
use Drupal\rp_api\Utility\RPAPIHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rp_api\ChangedFields\RPAPIChangedFields;
use Drupal\rp_api\RPAPIModelInterface;
use Drupal\rp_api\RPAPIModelBase;
use Drupal\rp_repoapi\RPRepoAPIClient;

/**
 * A base class to help developers implement their own RPAPIConfig plugins.
 *
 * @see \Drupal\rp_api\Annotation\RPAPIModel
 * @see \Drupal\rp_api\RPAPIModelInterface
 */
abstract class RPRepoAPIConfigBase extends RPAPIModelBase implements RPAPIModelInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function description() {
    // Retrieve the @description property from the annotation and return it.
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function entity() {
    return $this->pluginDefinition['entity'];
  }

  /**
   * {@inheritdoc}
   */
  public function api_method() {
    return $this->pluginDefinition['api_method'];
  }

  public function get_api_client() {
    return RPRepoAPIClient::getClient();
  }


}
