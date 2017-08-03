<?php

/**
 * @file
 * Contains language_content.page.inc.
 *
 * Page callback for Language content entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Language content templates.
 *
 * Default template: language_content.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_language_content(array &$variables) {
  // Fetch LanguageContent Entity Object.
  $language_content = $variables['elements']['#language_content'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
