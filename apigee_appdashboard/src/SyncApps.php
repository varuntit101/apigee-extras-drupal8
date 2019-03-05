<?php

namespace Drupal\apigee_appdashboard;

use Drupal\apigee_appdashboard\Entity\AppEntity;
use Drupal\Core\Controller\ControllerBase;
use Apigee\Edge\Api\Management\Controller\DeveloperController;
use Apigee\Edge\Api\Management\Entity\Developer;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use Apigee\Edge\Client;
use Http\Message\Authentication\BasicAuth;
use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Controller\AppController;
use Apigee\Edge\Api\Management\Controller\AppControllerInterface;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Structure\PagerInterface;
use Drupal\apigee_appdashboard\Controller\AppEntityController;


class SyncApps extends AppController {

  public static function deleteApp($eids) {
    $message = 'Deleting Cached Apps...';
    $results = array();

    foreach ($eids as $eid) {
      $app = AppEntity::load($eid);
      $results[] = $app->delete();
    }

    $context['message'] = $message;
    $context['results'] = $results;

    $message = 'Fetching Apps from Apigee Edge...';
    $results = array();

    // $cred = \Drupal::service('apigee_appdashboard.auth');
    // // dpm($cred->getUserName());
    // $username = $cred->getUserName();
    // $password = $cred->getPass();
    // $organization = $cred->getOrg();

    // $auth = new BasicAuth($username, $password);
    // $client = new Client($auth);

    //$dc = new DeveloperController($organization, $client);
    //$ac = new AppController($organization, $client);
    //$apps = $ac->listApps();
    //$apps = $ac->listEntities();

    $ac = \Drupal::service('apigee_appdashboard.app');

    $apps = $ac->listEntities();

    $dc = \Drupal::service('apigee_appdashboard.developer');

    //kint($entities);

   // kint($dc->listEntities());
   // kint($apps);

    foreach ($apps as $app) {
      $status = 'approved';
      //kint($app->getCredentials());
      $appCred = $app->getCredentials();
      //kint($appCred);
      foreach ($appCred as $productList) {
        //Set status to pending if one or more API Products is pending.
        foreach ($productList->getApiProducts() as $product) {
          //kint($product);
          if ($product->getStatus() == 'pending') {
            $status = 'pending';
            break;
          }
          if ($product->getStatus() == 'revoked') {
              $status = 'revoked';
              break;
          }
        }
        break;
      }
     // kint($app);
      $displayName = $app->getDisplayName();
      // $company = $app->getCompany();
      $created_at = $app->getCreatedAt()->getTimestamp();
      $modified_at = $app->getLastModifiedAt()->getTimestamp();
      $developer = $dc->getDeveloperByApp($displayName);
      $email = $developer->getEmail();
      $status = $app->getStatus();
      $appId = $app->getAppId();

      $ap = AppEntity::create([
        'field_app_id' => $appId,
        'name' => $displayName,
        'field_app_display_name' => $displayName,
        'field_created_at' => $created_at,
        'field_email' => $email,
        'field_modified_at' => $modified_at,
        //'field_organization_name' => $organization,
        'field_overall_status' => $status
      ]);

      $results[] = $ap->save();
    }
/**
    foreach ($apps as $app) {

      $displayName = $app->getDisplayName();
      // $company = $app->getCompany();
      $created_at = $app->getCreatedAt()->getTimestamp();
      $modified_at = $app->getLastModifiedAt()->getTimestamp();
      $developer = $dc->getDeveloperByApp($displayName);
      $email = $developer->getEmail();
      $status = $app->getStatus();
      $appId = $app->getAppId();

      $ap = AppEntity::create([
        'field_app_id' => $appId,
        'name' => $displayName,
        'field_app_display_name' => $displayName,
        'field_created_at' => $created_at,
        'field_email' => $email,
        'field_modified_at' => $modified_at,
        'field_organization_name' => $organization,
        'field_overall_status' => $status
      ]);

      $results[] = $ap->save();
    }
**/

    $context['message'] = $message;
    $context['results'] = $results;


  }
/*
  public function loadApp(&$context) {

    //$base = $context['results'];
    //dpm($base);

    $message = 'Fetching Apps from Apigee Edge...';
    $results = array();

    $cred = \Drupal::service('apigee_appdashboard.auth');
    // dpm($cred->getUserName());
    $username = $cred->getUserName();
    $password = $cred->getPass();
    $organization = $cred->getOrg();

    $auth = new BasicAuth($username, $password);
    $client = new Client($auth);

    $dc = new DeveloperController($organization, $client);
    $ac = new AppController($organization, $client);
    $apps = $ac->listApps();

    kint($dc);
/**
    foreach ($apps as $app) {

      $displayName = $app->getDisplayName();
      // $company = $app->getCompany();
      $created_at = $app->getCreatedAt()->getTimestamp();
      $modified_at = $app->getLastModifiedAt()->getTimestamp();
      $developer = $dc->getDeveloperByApp($displayName);
      $email = $developer->getEmail();
      $status = $app->getStatus();
      $appId = $app->getAppId();

      $ap = AppEntity::create([
        'field_app_id' => $appId,
        'name' => $displayName,
        'field_app_display_name' => $displayName,
        'field_created_at' => $created_at,
        'field_email' => $email,
        'field_modified_at' => $modified_at,
        'field_organization_name' => $organization,
        'field_overall_status' => $status
      ]);

      $results[] = $ap->save();
    }


    $context['message'] = $message;
    $context['results'] = $results;


    //drupal_set_message(hello);
  }
**/
  function deleteAppFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
    return new Url('<front>');
  }
}
