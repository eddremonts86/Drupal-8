<?php

/**
 * @file
 * Contains participant_info.page.inc.
 *
 * Page callback for Participant Info entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Participant Info templates.
 *
 * Default template: participant_info.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_participant_info(array &$variables) {
  // Fetch ParticipantInfo Entity Object.
  $participant_info = $variables['elements']['#participant_info'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
