<?php

namespace Drupal\apigee_appdashboard;

/**
 * Interface AuthenticationKeyServiceInterface.
 */
interface AuthenticationKeyServiceInterface {

  /**
   * Getting the organization name.
   */
  public function getOrg();

  /**
   * Getting the user name.
   */
  public function getUserName();

  /**
   * Getting the password.
   */
  public function getPass();

  /**
   * Getting the total authentication credentials.
   */
  public function getAuth();

  /**
   * Get the endpoint.
   */
  public function getEndpoint();

}
