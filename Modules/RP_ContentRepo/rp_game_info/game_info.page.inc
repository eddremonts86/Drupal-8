<?php

/**
 * @file
 * Contains game_info.page.inc.
 *
 * Page callback for Game info entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Game info templates.
 *
 * Default template: game_info.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_game_info(array &$variables) {
  // Fetch GameInfo Entity Object.
  $game_info = $variables['elements']['#game_info'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
