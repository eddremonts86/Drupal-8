<?php

namespace Drupal\rp_competition\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_competition\Entity\CompetitionInterface;

/**
 * Class CompetitionController.
 *
 *  Returns responses for Competition routes.
 *
 * @package Drupal\rp_competition\Controller
 */
class CompetitionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Competition  revision.
   *
   * @param int $competition_revision
   *   The Competition  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($competition_revision) {
    $competition = $this->entityManager()->getStorage('competition')->loadRevision($competition_revision);
    $view_builder = $this->entityManager()->getViewBuilder('competition');

    return $view_builder->view($competition);
  }

  /**
   * Page title callback for a Competition  revision.
   *
   * @param int $competition_revision
   *   The Competition  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($competition_revision) {
    $competition = $this->entityManager()->getStorage('competition')->loadRevision($competition_revision);
    return $this->t('Revision of %title from %date', ['%title' => $competition->label(), '%date' => format_date($competition->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Competition .
   *
   * @param \Drupal\rp_competition\Entity\CompetitionInterface $competition
   *   A Competition  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CompetitionInterface $competition) {
    $account = $this->currentUser();
    $langcode = $competition->language()->getId();
    $langname = $competition->language()->getName();
    $languages = $competition->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $competition_storage = $this->entityManager()->getStorage('competition');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $competition->label()]) : $this->t('Revisions for %title', ['%title' => $competition->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all competition revisions") || $account->hasPermission('administer competition entities')));
    $delete_permission = (($account->hasPermission("delete all competition revisions") || $account->hasPermission('administer competition entities')));

    $rows = [];

    $vids = $competition_storage->revisionIds($competition);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_competition\CompetitionInterface $revision */
      $revision = $competition_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $competition->getRevisionId()) {
          $link = $this->l($date, new Url('entity.competition.revision', ['competition' => $competition->id(), 'competition_revision' => $vid]));
        }
        else {
          $link = $competition->link($date);
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
              Url::fromRoute('entity.competition.translation_revert', ['competition' => $competition->id(), 'competition_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.competition.revision_revert', ['competition' => $competition->id(), 'competition_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.competition.revision_delete', ['competition' => $competition->id(), 'competition_revision' => $vid]),
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

    $build['competition_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
