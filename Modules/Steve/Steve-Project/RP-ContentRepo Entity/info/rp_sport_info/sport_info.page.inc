<?php

/**
 * @file
 * Contains sport_info.page.inc.
 *
 * Page callback for Sport info entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Sport info templates.
 *
 * Default template: sport_info.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_sport_info(array &$variables) {
  // Fetch SportInfo Entity Object.
  $sport_info = $variables['elements']['#sport_info'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
