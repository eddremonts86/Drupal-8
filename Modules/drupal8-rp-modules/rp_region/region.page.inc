<?php

/**
 * @file
 * Contains region.page.inc.
 *
 * Page callback for Region entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Region templates.
 *
 * Default template: region.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_region(array &$variables) {
  // Fetch region Entity Object.
  $region = $variables['elements']['#region'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
