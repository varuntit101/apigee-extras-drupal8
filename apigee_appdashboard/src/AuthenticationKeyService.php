<?php

namespace Drupal\apigee_appdashboard;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\key\Entity\Key;

/**
 * Class AuthenticationKeyService.
 */
class AuthenticationKeyService implements AuthenticationKeyServiceInterface {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new AuthenticationKeyService object.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory->get('apigee_edge.auth');
  }

  /**
   * Helper function to get the keys.
   *
   * @return mixed
   *   The
   */
  protected function getAvailablekeys() {
    $key_name = $this->configFactory->getRawData()['active_key'];
    if (!empty($key_name)) {
      $availableKeys = Key::load($key_name)->getKeyValues();
      return $availableKeys;
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOrg() {
    return $this->getAvailablekeys()['organization'];
  }

  /**
   * {@inheritdoc}
   */
  public function getUserName() {
    return $this->getAvailablekeys()['username'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPass() {
    return $this->getAvailablekeys()['password'];
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthType() {
    return $this->getAvailablekeys()['auth_type'];
  }

  /**
   * {@inheritdoc}
   */
  public function getAuth() {
    $auth = [
      $this->getUserName(),
      $this->getPass(),
    ];
    return $auth;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoint() {
    $endpoint = $this->getAvailablekeys()['endpoint'];
    if (!empty($endpoint)) {
      return $endpoint;
    }
    else {
      return "https://api.enterprise.apigee.com/v1";
    }
  }

}
