<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the App entity.
 *
 * @see \Drupal\apigee_appdashboard\Entity\AppEntity.
 */
class AppEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\apigee_appdashboard\Entity\AppEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished app entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published app entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit app entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete app entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add app entities');
  }

}
