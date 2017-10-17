<?php

namespace Drupal\rp_sport_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_sport_info\Entity\SportInfoInterface;

/**
 * Class SportInfoController.
 *
 *  Returns responses for Sport info routes.
 *
 * @package Drupal\rp_sport_info\Controller
 */
class SportInfoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Sport info  revision.
   *
   * @param int $sport_info_revision
   *   The Sport info  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($sport_info_revision) {
    $sport_info = $this->entityManager()->getStorage('sport_info')->loadRevision($sport_info_revision);
    $view_builder = $this->entityManager()->getViewBuilder('sport_info');

    return $view_builder->view($sport_info);
  }

  /**
   * Page title callback for a Sport info  revision.
   *
   * @param int $sport_info_revision
   *   The Sport info  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($sport_info_revision) {
    $sport_info = $this->entityManager()->getStorage('sport_info')->loadRevision($sport_info_revision);
    return $this->t('Revision of %title from %date', ['%title' => $sport_info->label(), '%date' => format_date($sport_info->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Sport info .
   *
   * @param \Drupal\rp_sport_info\Entity\SportInfoInterface $sport_info
   *   A Sport info  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(SportInfoInterface $sport_info) {
    $account = $this->currentUser();
    $langcode = $sport_info->language()->getId();
    $langname = $sport_info->language()->getName();
    $languages = $sport_info->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $sport_info_storage = $this->entityManager()->getStorage('sport_info');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $sport_info->label()]) : $this->t('Revisions for %title', ['%title' => $sport_info->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all sport info revisions") || $account->hasPermission('administer sport info entities')));
    $delete_permission = (($account->hasPermission("delete all sport info revisions") || $account->hasPermission('administer sport info entities')));

    $rows = [];

    $vids = $sport_info_storage->revisionIds($sport_info);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_sport_info\SportInfoInterface $revision */
      $revision = $sport_info_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $sport_info->getRevisionId()) {
          $link = $this->l($date, new Url('entity.sport_info.revision', ['sport_info' => $sport_info->id(), 'sport_info_revision' => $vid]));
        }
        else {
          $link = $sport_info->link($date);
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
              Url::fromRoute('entity.sport_info.translation_revert', ['sport_info' => $sport_info->id(), 'sport_info_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.sport_info.revision_revert', ['sport_info' => $sport_info->id(), 'sport_info_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.sport_info.revision_delete', ['sport_info' => $sport_info->id(), 'sport_info_revision' => $vid]),
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

    $build['sport_info_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
