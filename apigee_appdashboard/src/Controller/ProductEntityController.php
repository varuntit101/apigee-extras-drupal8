<?php

namespace Drupal\apigee_appdashboard\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\apigee_appdashboard\Entity\ProductEntityInterface;

/**
 * Class ProductEntityController.
 *
 *  Returns responses for Product routes.
 */
class ProductEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Product  revision.
   *
   * @param int $product_entity_revision
   *   The Product  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($product_entity_revision) {
    $product_entity = $this->entityManager()->getStorage('product_entity')->loadRevision($product_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('product_entity');

    return $view_builder->view($product_entity);
  }

  /**
   * Page title callback for a Product  revision.
   *
   * @param int $product_entity_revision
   *   The Product  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($product_entity_revision) {
    $product_entity = $this->entityManager()->getStorage('product_entity')->loadRevision($product_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $product_entity->label(), '%date' => format_date($product_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Product .
   *
   * @param \Drupal\apigee_appdashboard\Entity\ProductEntityInterface $product_entity
   *   A Product  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ProductEntityInterface $product_entity) {
    $account = $this->currentUser();
    $langcode = $product_entity->language()->getId();
    $langname = $product_entity->language()->getName();
    $languages = $product_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $product_entity_storage = $this->entityManager()->getStorage('product_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $product_entity->label()]) : $this->t('Revisions for %title', ['%title' => $product_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all product revisions") || $account->hasPermission('administer product entities')));
    $delete_permission = (($account->hasPermission("delete all product revisions") || $account->hasPermission('administer product entities')));

    $rows = [];

    $vids = $product_entity_storage->revisionIds($product_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\apigee_appdashboard\ProductEntityInterface $revision */
      $revision = $product_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $product_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.product_entity.revision', ['product_entity' => $product_entity->id(), 'product_entity_revision' => $vid]));
        }
        else {
          $link = $product_entity->link($date);
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
              Url::fromRoute('entity.product_entity.translation_revert', ['product_entity' => $product_entity->id(), 'product_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.product_entity.revision_revert', ['product_entity' => $product_entity->id(), 'product_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.product_entity.revision_delete', ['product_entity' => $product_entity->id(), 'product_entity_revision' => $vid]),
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

    $build['product_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
