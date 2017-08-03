<?php

/**
 * @file
 * Contains client_site.page.inc.
 *
 * Page callback for Client_site entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Client_site templates.
 *
 * Default template: client_site.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_client_site(array &$variables) {
  // Fetch client_site Entity Object.
  $client_site = $variables['elements']['#client_site'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
