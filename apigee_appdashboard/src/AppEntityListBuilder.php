<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of App entities.
 *
 * @ingroup apigee_appdashboard
 */
class AppEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('App ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\apigee_appdashboard\Entity\AppEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.app_entity.edit_form',
      ['app_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
