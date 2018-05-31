<?php

/**
 * @file
 * Contains competition.page.inc.
 *
 * Page callback for Competition entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Competition templates.
 *
 * Default template: competition.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_competition(array &$variables) {
  // Fetch Competition Entity Object.
  $competition = $variables['elements']['#competition'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
