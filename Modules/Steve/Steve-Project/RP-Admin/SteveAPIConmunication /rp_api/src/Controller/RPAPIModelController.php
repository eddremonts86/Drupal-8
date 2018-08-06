<?php

namespace Drupal\rp_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rp_api\RPAPIModelManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for our example pages.
 */
class RPAPIModelController extends ControllerBase {

  /**
   * The RPAPIModel plugin manager.
   *
   * We use this to get all of the RPAPIModel plugins.
   *
   * @var \Drupal\rp_api\RPAPIModelManager
   */
  protected $RPAPIModelManager;

  /**
   * Constructor.
   *
   * @param \Drupal\rp_api\RPAPIModelManager $RPAPIModel_manager
   *   The RPAPIModel plugin manager service. We're injecting this service so that
   *   we can use it to access the RPAPIModel plugins.
   */
  public function __construct(RPAPIModelManager $RPAPIModel_manager) {
    $this->RPAPIModelManager = $RPAPIModel_manager;
  }

  /**
   * Displays a page with an overview of our plugin type and plugins.
   *
   * Lists all the RPAPIModel plugin definitions by using methods on the
   * \Drupal\rp_api\RPAPIModelManager class. Lists out the
   * description for each plugin found by invoking methods defined on the
   * plugins themselves. You can find the plugins we have defined in the
   * \Drupal\rp_api\Plugin\RPAPIModel namespace.
   *
   * @return array
   *   Render API array with content for the page at
   *   /examples/rp_api.
   */
  public function description() {
    $build = array();

    $build['intro'] = array(
      '#markup' => t("This page lists the RPAPIModel plugins we've created. The RPAPIModel plugin type is defined in Drupal\\rp_api\\RPAPIModelManager. The various plugins are defined in the Drupal\\rp_api\\Plugin\\RPAPIModel namespace."),
    );

    $RPAPIModel_plugin_definitions = $this->RPAPIModelManager->getDefinitions();

    $items = array();
    foreach ($RPAPIModel_plugin_definitions as $RPAPIModel_plugin_definition) {
      $items[] = t("@id (description: @description )", array(
        '@id' => $RPAPIModel_plugin_definition['id'],
        '@description' => $RPAPIModel_plugin_definition['description'],
      ));
    }

    // Add our list to the render array.
    $build['plugin_definitions'] = array(
      '#theme' => 'item_list',
      '#title' => 'RPAPIModel plugin definitions',
      '#items' => $items,
    );

    //$example_RPAPIModel_plugin_definition = $this->RPAPIModelManager->getDefinition('example_rpapimodel');

    // To get an instance of a plugin, we call createInstance() on the plugin
    // manager, passing the ID of the plugin we want to load. Let's output a
    // list of the plugins by loading an instance of each plugin definition and
    // collecting the description from each.
    $items = array();
    // The array of plugin definitions is keyed by plugin id, so we can just use
    // that to load our plugin instances.
    foreach ($RPAPIModel_plugin_definitions as $plugin_id => $RPAPIModel_plugin_definition) {
      // We now have a plugin instance. From here on it can be treated just as
      // any other object; have its properties examined, methods called, etc.
      $plugin = $this->RPAPIModelManager->createInstance($plugin_id, array('of' => 'configuration values'));
      $items[] = $plugin->description();
    }

    $build['plugins'] = array(
      '#theme' => 'item_list',
      '#title' => 'RPAPIModel plugins',
      '#items' => $items,
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Override the parent method so that we can inject our RPAPIModel plugin
   * manager service into the controller.
   *
   * For more about how dependency injection works read
   * https://www.drupal.org/node/2133171
   *
   * @see container
   */
  public static function create(ContainerInterface $container) {
    // Inject the plugin.manager.RPAPIModel service that represents our plugin
    // manager as defined in the rp_api.services.yml file.
    return new static($container->get('rp_api.plugin.model.manager'));
  }

}
