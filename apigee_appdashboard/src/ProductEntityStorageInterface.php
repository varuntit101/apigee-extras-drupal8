<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ProductEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Product revision IDs for a specific Product.
   *
   * @param \Drupal\apigee_appdashboard\Entity\ProductEntityInterface $entity
   *   The Product entity.
   *
   * @return int[]
   *   Product revision IDs (in ascending order).
   */
  public function revisionIds(ProductEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Product author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Product revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\apigee_appdashboard\Entity\ProductEntityInterface $entity
   *   The Product entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ProductEntityInterface $entity);

  /**
   * Unsets the language for all Product with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
