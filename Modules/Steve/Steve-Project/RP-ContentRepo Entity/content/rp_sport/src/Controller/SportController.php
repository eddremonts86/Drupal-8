<?php

namespace Drupal\rp_sport\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\rp_sport\Entity\SportInterface;

/**
 * Class SportController.
 *
 *  Returns responses for Sport routes.
 *
 * @package Drupal\rp_sport\Controller
 */
class SportController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Sport  revision.
   *
   * @param int $sport_revision
   *   The Sport  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($sport_revision) {
    $sport = $this->entityManager()->getStorage('sport')->loadRevision($sport_revision);
    $view_builder = $this->entityManager()->getViewBuilder('sport');

    return $view_builder->view($sport);
  }

  /**
   * Page title callback for a Sport  revision.
   *
   * @param int $sport_revision
   *   The Sport  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($sport_revision) {
    $sport = $this->entityManager()->getStorage('sport')->loadRevision($sport_revision);
    return $this->t('Revision of %title from %date', ['%title' => $sport->label(), '%date' => format_date($sport->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Sport .
   *
   * @param \Drupal\rp_sport\Entity\SportInterface $sport
   *   A Sport  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(SportInterface $sport) {
    $account = $this->currentUser();
    $langcode = $sport->language()->getId();
    $langname = $sport->language()->getName();
    $languages = $sport->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $sport_storage = $this->entityManager()->getStorage('sport');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $sport->label()]) : $this->t('Revisions for %title', ['%title' => $sport->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all sport revisions") || $account->hasPermission('administer sport entities')));
    $delete_permission = (($account->hasPermission("delete all sport revisions") || $account->hasPermission('administer sport entities')));

    $rows = [];

    $vids = $sport_storage->revisionIds($sport);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\rp_sport\SportInterface $revision */
      $revision = $sport_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $sport->getRevisionId()) {
          $link = $this->l($date, new Url('entity.sport.revision', ['sport' => $sport->id(), 'sport_revision' => $vid]));
        }
        else {
          $link = $sport->link($date);
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
              Url::fromRoute('entity.sport.translation_revert', ['sport' => $sport->id(), 'sport_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.sport.revision_revert', ['sport' => $sport->id(), 'sport_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.sport.revision_delete', ['sport' => $sport->id(), 'sport_revision' => $vid]),
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

    $build['sport_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
