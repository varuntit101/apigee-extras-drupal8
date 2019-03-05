<?php

namespace Drupal\apigee_appdashboard\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\apigee_appdashboard\Entity\AppEntityInterface;

/**
 * Class AppEntityController.
 *
 *  Returns responses for App routes.
 */
class AppEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a App  revision.
   *
   * @param int $app_entity_revision
   *   The App  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($app_entity_revision) {
    $app_entity = $this->entityManager()->getStorage('app_entity')->loadRevision($app_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('app_entity');

    return $view_builder->view($app_entity);
  }

  /**
   * Page title callback for a App  revision.
   *
   * @param int $app_entity_revision
   *   The App  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($app_entity_revision) {
    $app_entity = $this->entityManager()->getStorage('app_entity')->loadRevision($app_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $app_entity->label(), '%date' => format_date($app_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a App .
   *
   * @param \Drupal\apigee_appdashboard\Entity\AppEntityInterface $app_entity
   *   A App  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AppEntityInterface $app_entity) {
    $account = $this->currentUser();
    $langcode = $app_entity->language()->getId();
    $langname = $app_entity->language()->getName();
    $languages = $app_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $app_entity_storage = $this->entityManager()->getStorage('app_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $app_entity->label()]) : $this->t('Revisions for %title', ['%title' => $app_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all app revisions") || $account->hasPermission('administer app entities')));
    $delete_permission = (($account->hasPermission("delete all app revisions") || $account->hasPermission('administer app entities')));

    $rows = [];

    $vids = $app_entity_storage->revisionIds($app_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\apigee_appdashboard\AppEntityInterface $revision */
      $revision = $app_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $app_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.app_entity.revision', ['app_entity' => $app_entity->id(), 'app_entity_revision' => $vid]));
        }
        else {
          $link = $app_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.app_entity.translation_revert', ['app_entity' => $app_entity->id(), 'app_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.app_entity.revision_revert', ['app_entity' => $app_entity->id(), 'app_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.app_entity.revision_delete', ['app_entity' => $app_entity->id(), 'app_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['app_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
