<?php

/**
 * @file
 * Contains competition_info.page.inc.
 *
 * Page callback for Competition Info entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Competition Info templates.
 *
 * Default template: competition_info.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_competition_info(array &$variables) {
  // Fetch CompetitionInfo Entity Object.
  $competition_info = $variables['elements']['#competition_info'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
