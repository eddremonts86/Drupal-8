<?php

/**
 * @file
 * Contains site_info.page.inc.
 *
 * Page callback for Site info entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Site info templates.
 *
 * Default template: site_info.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_site_info(array &$variables) {
  // Fetch SiteInfo Entity Object.
  $site_info = $variables['elements']['#site_info'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
