<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface AppEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of App revision IDs for a specific App.
   *
   * @param \Drupal\apigee_appdashboard\Entity\AppEntityInterface $entity
   *   The App entity.
   *
   * @return int[]
   *   App revision IDs (in ascending order).
   */
  public function revisionIds(AppEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as App author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   App revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\apigee_appdashboard\Entity\AppEntityInterface $entity
   *   The App entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(AppEntityInterface $entity);

  /**
   * Unsets the language for all App with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
