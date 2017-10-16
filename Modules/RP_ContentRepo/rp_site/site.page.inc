<?php

/**
 * @file
 * Contains site.page.inc.
 *
 * Page callback for Site entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Site templates.
 *
 * Default template: site.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_site(array &$variables) {
  // Fetch Site Entity Object.
  $site = $variables['elements']['#site'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
