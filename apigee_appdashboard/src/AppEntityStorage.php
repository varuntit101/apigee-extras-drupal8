<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\apigee_appdashboard\Entity\AppEntityInterface;

/**
 * Defines the storage handler class for App entities.
 *
 * This extends the base storage class, adding required special handling for
 * App entities.
 *
 * @ingroup apigee_appdashboard
 */
class AppEntityStorage extends SqlContentEntityStorage implements AppEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(AppEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {app_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {app_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(AppEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {app_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('app_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
