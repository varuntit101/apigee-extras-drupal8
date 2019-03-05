<?php

namespace Drupal\apigee_appdashboard;


use Apigee\Edge\Api\Management\Controller\AppController;
use Apigee\Edge\Client;
use Http\Message\Authentication\BasicAuth;

/**
 * Class App.
 */
class App extends AppController implements AppInterface {

  /**
   * Constructs a new App object.
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
