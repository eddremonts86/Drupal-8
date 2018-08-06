<?php

namespace Drupal\rp_competition_info\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_competition_info\Entity\CompetitionInfoInterface;

/**
 * Class CompetitionInfoController.
 *
 *  Returns responses for Competition Info routes.
 *
 * @package Drupal\rp_competition_info\Controller
 */
class CompetitionInfoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Competition Info  revision.
   *
   * @param int $competition_info_revision
   *   The Competition Info  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($competition_info_revision) {
    $competition_info = $this->entityManager()->getStorage('competition_info')->loadRevision($competition_info_revision);
    $view_builder = $this->entityManager()->getViewBuilder('competition_info');

    return $view_builder->view($competition_info);
  }

  /**
   * Page title callback for a Competition Info  revision.
   *
   * @param int $competition_info_revision
   *   The Competition Info  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($competition_info_revision) {
    $competition_info = $this->entityManager()->getStorage('competition_info')->loadRevision($competition_info_revision);
    return $this->t('Revision of %title from %date', ['%title' => $competition_info->label(), '%date' => format_date($competition_info->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Competition Info .
   *
   * @param \Drupal\rp_competition_info\Entity\CompetitionInfoInterface $competition_info
   *   A Competition Info  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CompetitionInfoInterface $competition_info) {
    $account = $this->currentUser();
    $langcode = $competition_info->language()->getId();
    $langname = $competition_info->language()->getName();
    $languages = $competition_info->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $competition_info_storage = $this->entityManager()->getStorage('competition_info');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $competition_info->label()]) : $this->t('Revisions for %title', ['%title' => $competition_info->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all competition info revisions") || $account->hasPermission('administer competition info entities')));
    $delete_permission = (($account->hasPermission("delete all competition info revisions") || $account->hasPermission('administer competition info entities')));

    $rows = [];

    $vids = $competition_info_storage->revisionIds($competition_info);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_competition_info\CompetitionInfoInterface $revision */
      $revision = $competition_info_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $competition_info->getRevisionId()) {
          $link = $this->l($date, new Url('entity.competition_info.revision', ['competition_info' => $competition_info->id(), 'competition_info_revision' => $vid]));
        }
        else {
          $link = $competition_info->link($date);
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
              Url::fromRoute('entity.competition_info.translation_revert', ['competition_info' => $competition_info->id(), 'competition_info_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.competition_info.revision_revert', ['competition_info' => $competition_info->id(), 'competition_info_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.competition_info.revision_delete', ['competition_info' => $competition_info->id(), 'competition_info_revision' => $vid]),
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

    $build['competition_info_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
