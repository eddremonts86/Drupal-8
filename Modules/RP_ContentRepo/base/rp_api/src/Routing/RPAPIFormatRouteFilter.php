<?php

namespace Drupal\rp_api\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
//use Drupal\Core\Routing\RouteFilterInterface;

use Drupal\Core\Routing\FilterInterface;

use Drupal\rest\Plugin\Type\ResourcePluginManager;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Overrides the functionality of RequestFormatRouteFilter.
 */
class RPAPIFormatRouteFilter implements FilterInterface {
  /**
   * The route filter.
   *
   * @var \Drupal\Core\Routing\FilterInterface
   */
  protected $requestFormatRouteFilter;

  /**
   * Rest resource plugin manager.
   *
   * @var \Drupal\rest\Plugin\Type\ResourcePluginManager
   */
  protected $resourcePluginManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * RestWithoutFormatRouteFilter constructor.
   * @param \Drupal\Core\Routing\FilterInterface $request_format_route_filter
   *   The route filter.
   * @param \Drupal\rest\Plugin\Type\ResourcePluginManager $resource_plugin_manager
   *   The rest plugin manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(FilterInterface $request_format_route_filter, ResourcePluginManager $resource_plugin_manager, ModuleHandlerInterface $module_handler) {
    $this->requestFormatRouteFilter = $request_format_route_filter;
    $this->resourcePluginManager = $resource_plugin_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Route $route) {
    return $route->hasRequirement('_format');
  }

  /**
   * {@inheritdoc}
   */
  public function filter(RouteCollection $collection, Request $request) {

    $rest_plugins = $this->resourcePluginManager->getDefinitions();

    $endpoints = [];

    foreach ($rest_plugins as $rest_name => $rest_plugin) {
      $endpoints[] = @$rest_plugin['uri_paths']['canonical'];
    }

    // If no rest resource, skipped.
    if (empty($endpoints)) {
      return $this->requestFormatRouteFilter->filter($collection, $request);
    }

    $format = $request->getRequestFormat('json');

    $this->moduleHandler->alter('rp_api', $collection, $request, $endpoints);

    $rest_routes = [];

    /** @var \Symfony\Component\Routing\Route $route */
    foreach ($collection as $name => $route) {

      $route_path = $route->getPath();

      if (in_array($route_path, $endpoints)) {
        $route_format = $route->getRequirement('_format');
        $route->setRequirement('_format', 'json');
        $rest_routes[] = $route_path;
      }

    }

    // If there is any route found.
    if (count($rest_routes)) {
      return $collection;
    }

    // If nothing found, use original route filter behavior.
    return $this->requestFormatRouteFilter->filter($collection, $request);
  }

}
