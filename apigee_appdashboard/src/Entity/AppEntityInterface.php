<?php

namespace Drupal\apigee_appdashboard\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining App entities.
 *
 * @ingroup apigee_appdashboard
 */
interface AppEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the App name.
   *
   * @return string
   *   Name of the App.
   */
  public function getName();

  /**
   * Sets the App name.
   *
   * @param string $name
   *   The App name.
   *
   * @return \Drupal\apigee_appdashboard\Entity\AppEntityInterface
   *   The called App entity.
   */
  public function setName($name);

  /**
   * Gets the App creation timestamp.
   *
   * @return int
   *   Creation timestamp of the App.
   */
  public function getCreatedTime();

  /**
   * Sets the App creation timestamp.
   *
   * @param int $timestamp
   *   The App creation timestamp.
   *
   * @return \Drupal\apigee_appdashboard\Entity\AppEntityInterface
   *   The called App entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the App published status indicator.
   *
   * Unpublished App are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the App is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a App.
   *
   * @param bool $published
   *   TRUE to set this App to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\apigee_appdashboard\Entity\AppEntityInterface
   *   The called App entity.
   */
  public function setPublished($published);

  /**
   * Gets the App revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the App revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\apigee_appdashboard\Entity\AppEntityInterface
   *   The called App entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the App revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the App revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\apigee_appdashboard\Entity\AppEntityInterface
   *   The called App entity.
   */
  public function setRevisionUserId($uid);

}
