<?php

/**
 * @file
 * Contains stream_provider_info.page.inc.
 *
 * Page callback for Stream Provider Info entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Stream Provider Info templates.
 *
 * Default template: stream_provider_info.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_stream_provider_info(array &$variables) {
  // Fetch StreamProviderInfo Entity Object.
  $stream_provider_info = $variables['elements']['#stream_provider_info'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
