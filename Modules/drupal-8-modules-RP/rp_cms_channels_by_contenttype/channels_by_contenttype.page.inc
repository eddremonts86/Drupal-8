<?php

/**
 * @file
 * Contains channels_by_contenttype.page.inc.
 *
 * Page callback for Channels_by_contenttype entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Channels_by_contenttype templates.
 *
 * Default template: channels_by_contenttype.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_channels_by_contenttype(array &$variables) {
  // Fetch channels_by_contenttype Entity Object.
  $channels_by_contenttype = $variables['elements']['#channels_by_contenttype'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
