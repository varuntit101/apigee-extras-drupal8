<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\apigee_appdashboard\Entity\ProductEntityInterface;

/**
 * Defines the storage handler class for Product entities.
 *
 * This extends the base storage class, adding required special handling for
 * Product entities.
 *
 * @ingroup apigee_appdashboard
 */
class ProductEntityStorage extends SqlContentEntityStorage implements ProductEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ProductEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {product_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {product_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ProductEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {product_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('product_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
