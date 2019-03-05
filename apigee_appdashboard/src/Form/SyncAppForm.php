<?php
namespace Drupal\apigee_appdashboard\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\apigee_appdashboard\Entity\AppEntity;
use Drupal\Core\Url;

/**
 * Class SyncAppForm.
 *
 * @package Drupal\apigee_appdashboard\Form
 */
class SyncAppForm extends ConfirmFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sync_app_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // $form['sync_app'] = array(
    //   '#type' => 'submit',
    //   '#value' => $this->t('Sync Apps'),
    // );
    // return $form;
    return parent::buildForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $eids = \Drupal::entityQuery('app_entity')
      ->sort('created', 'ASC')
      ->execute();
    // $apps = \Drupal::entityManager()
    //   ->getStorage('app_entity')
    //   ->loadMultiple();

    //kint($eids->loadMultiple());
    //kint($nids);

    $batch = array(
      'title' => t('Syncing Apps With Apigee Edge...'),
      'operations' => array(
        array(
          '\Drupal\apigee_appdashboard\SyncApps::deleteApp',
          array($eids)
        ),
        //array('\Drupal\apigee_appdashboard\SyncApps::loadApp',
        //  array(),
        //),
      ),
      'finished' => '\Drupal\batch_example\SyncApps::deleteAppFinishedCallback',
    );
    batch_set($batch);

    //return \Drupal\apigee_appdashboard\SyncApps::deleteApp($eids);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('<front>');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you want to sync the apps from Edge?');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('This is a long running process. This will delete all the existing entries and resync from edge.');
  }

}
