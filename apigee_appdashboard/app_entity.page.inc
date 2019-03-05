<?php

/**
 * @file
 * Contains app_entity.page.inc.
 *
 * Page callback for App entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for App templates.
 *
 * Default template: app_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_app_entity(array &$variables) {
  // Fetch AppEntity Entity Object.
  $app_entity = $variables['elements']['#app_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
