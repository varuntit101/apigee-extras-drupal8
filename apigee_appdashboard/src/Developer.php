<?php

namespace Drupal\apigee_appdashboard;

use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Client;
use Http\Message\Authentication\BasicAuth;

/**
 * Class Developer.
 */
class Developer extends DeveloperController implements DeveloperInterface {

  /**
   * Constructs a new Developer object.
   */
  public function __construct() {

    $cred = \Drupal::service('apigee_appdashboard.auth');

    $username = $cred->getUserName();
    $password = $cred->getPass();
    $organization = $cred->getOrg();


    $auth = new BasicAuth($username, $password);
    $client = new Client($auth);

    parent::__construct($organization, $client);
  }

}
