<?php

/**
 * @file
 * Contains stream_provider.page.inc.
 *
 * Page callback for Stream Provider entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Stream Provider templates.
 *
 * Default template: stream_provider.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_stream_provider(array &$variables) {
  // Fetch StreamProvider Entity Object.
  $stream_provider = $variables['elements']['#stream_provider'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
