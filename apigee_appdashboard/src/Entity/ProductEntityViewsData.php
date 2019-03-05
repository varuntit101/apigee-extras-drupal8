<?php

namespace Drupal\apigee_appdashboard\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Product entities.
 */
class ProductEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
